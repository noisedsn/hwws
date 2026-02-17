/*
  Sketch Title: HWWS firmware (ESP8266 version)
  Description: Hello World Weather Sensor.
  Author: noisedsn
  Version: 0.9
*/

#include <WiFi.h>
#include <Adafruit_BME280.h>
#include <HTTPClient.h>
#include <WebServer.h>
#include <WiFiClientSecure.h>  // Required for HTTPS
#include <Preferences.h>
#include <MD5Builder.h>
#include "html_template.h"  // Access Point html templates

#define LED 8   // LED pin
#define PWR 10  // Pin that will power the sensor (for battery-saving)
#define BAT A0  // Analog channel to measure battery voltage
#define SEALEVELPRESSURE_HPA (1013.25)
#define RESET_FLAG 0xA5
#define DOUBLE_RESET_DELAY_MS 1000
#define RW_MODE false
#define RO_MODE true

const String ap_ssid = "Weather Sensor";  // Initial access point network credentials
const String ap_pass = "123456789";
const float dividerRatio = 0.5;           // Resistor divider ratio ractor. Tweak it slightly to get correct battery voltage.

// Other global variables
String serverHost;
String serverPath;
String pinCode;
int sleepTime;
String sessionCookie = "";

// Preferences object
Preferences prefs;


// Function declarations
bool putValue(int address, int length, String value);
void handleWebServer();
bool testWiFi();
String scanWiFi();
void deepSleep();


// Setup function
void setup() {

  pinMode(LED, OUTPUT);  // Initialize the LED pin as an output
  pinMode(PWR, OUTPUT);  // Initialize the PWR pin as an output
  pinMode(BAT, INPUT);   // Initialize the BAT pin as an input

  digitalWrite(PWR, HIGH);
  digitalWrite(LED, LOW);  // Low = thrned on

  analogReadResolution(12);
  analogSetAttenuation(ADC_11db);

  Serial.begin(115200);

  delay(10);
  Serial.println();
  Serial.println("Starting");
  Serial.println();

  // Read preferences
  uint8_t resetFlag;

  prefs.begin("sensor", RW_MODE);

  if (!prefs.isKey("wifiSsid")) {
    // First boot, configuring required
    resetFlag = RESET_FLAG;
  } else {
    String wifiSsid = prefs.getString("wifiSsid");
    // Serial.println("wifiSsid: " + wifiSsid);

    String wifiPass = prefs.getString("wifiPass");
    // Serial.println("wifiPass: " + wifiPass);

    serverHost = prefs.getString("serverHost");
    // Serial.println("serverHost: " + serverHost);

    serverPath = prefs.getString("serverPath");
    // Serial.println("serverPath: " + serverPath);

    pinCode = prefs.getString("pinCode");
    // Serial.println("pinCode: " + pinCode);

    sleepTime = prefs.getInt("sleepTime");
    // Serial.println("sleepTime: " + sleepTime);

    resetFlag = prefs.getUChar("resetFlag");
    // Serial.println("resetFlag: " + (String)resetFlag);
  
    WiFi.begin(wifiSsid.c_str(), wifiPass.c_str());
  }

  // Double-click on reset magic starts here
  // Ignore deep sleep wakeups
  if (esp_sleep_get_wakeup_cause() != ESP_SLEEP_WAKEUP_UNDEFINED) {
    return;
  }

  if (resetFlag == RESET_FLAG) {
    // Second reset → double click
    // Serial.println("Clearing resetFlag");
    prefs.putUChar("resetFlag", 0);

    Serial.println("Turning the Access Point On");
    WiFi.mode(WIFI_STA);
    WiFi.softAP(ap_ssid, ap_pass);

    Serial.println();
    Serial.println("--------------------------------------------");
    Serial.println("To configure the sensor, connect to the next");
    Serial.println("Access Point and open IP address in browser:");
    Serial.println("--------------------------------------------");
    Serial.println("SSID: " + (String)ap_ssid);
    Serial.println("Pass: " + (String)ap_pass);
    Serial.println("IP:   " + WiFi.softAPIP().toString());
    Serial.println();

    handleWebServer();

    return;
  }

  // First reset
  // Serial.println("Raising resetFlag");
  prefs.putUChar("resetFlag", RESET_FLAG);

  // Give time for second reset
  delay(DOUBLE_RESET_DELAY_MS);

  // No second reset → clear flag
  // Serial.println("Clearing resetFlag");
  prefs.putUChar("resetFlag", 0);
  prefs.end();
}


// Main function
void loop() {

  digitalWrite(LED, HIGH);

  // Taking measurements
  float temperature;
  float pressure;
  float altitude;
  float humidity;
  float millivolts = 0.0;
  float voltage = 0.0;

  Adafruit_BME280 bme;  // I2C

  if (bme.begin(0x76)) {
    temperature = bme.readTemperature();
    Serial.println("temperature: " + (String)temperature);

    pressure = (bme.readPressure() / 100.0F);
    Serial.println("pressure: " + (String)pressure);

    altitude = bme.readAltitude(SEALEVELPRESSURE_HPA);
    Serial.println("altitude: " + (String)altitude);

    humidity = bme.readHumidity();
    Serial.println("humidity: " + (String)humidity);
  } else {
    Serial.println("Sensor unavailable");
    deepSleep();
  }

  for (unsigned int i = 0; i < 5; i++) {
    millivolts += analogReadMilliVolts(BAT);  // Read analog Voltage
    delay(5);                                // Wait for ADC to stabilize
  }
  voltage = millivolts / (dividerRatio * 5000);  // battery voltage by multiplying by resistor divider ratio
  Serial.println("voltage: " + (String)voltage);

  pinMode(LED, OUTPUT);
  digitalWrite(LED, LOW);

  // Sending data to server
  if (testWiFi()) {

    // Use WiFiClient class to create TCP connections
    WiFiClientSecure client;
    const int httpPort = 443;  // 80 is for HTTP / 443 is for HTTPS!

    client.setInsecure();

    if (client.connect(serverHost.c_str(), httpPort)) {

      // First connect without data to get authorization headers
      String url = "https://" + serverHost + serverPath;
      String httpRequestData;

      // Starting HTTPClient
      HTTPClient http;
      http.begin(client, url);

      const char * headerkeys[] = {"Set-Cookie", "Cookie", "X-Custom-Auth"};
      size_t headerkeyssize = sizeof(headerkeys) / sizeof(char*);
      http.collectHeaders(headerkeys, headerkeyssize);

      // Send HTTP GET request
      int httpResponseCode = http.GET();

      // Authorization
      if (http.hasHeader("X-Custom-Auth")) {
        String nonce = http.header("X-Custom-Auth");
        // Serial.println("nonce: " + nonce);

        // Sign the request with pinCode
        MD5Builder md;

        md.begin();
        md.add(pinCode+nonce+temperature+pressure+altitude+humidity+voltage);
        md.calculate();

        String signature = md.toString();

        httpRequestData = (String)"t=" + temperature + "&p=" + pressure + "&a=" + altitude + "&h=" + humidity + "&v=" + voltage + "&s=" + signature;
        Serial.println(url);
      }


      // Set cookie
      if (http.hasHeader("Set-Cookie")) {
        sessionCookie = http.header("Set-Cookie");
        // Serial.printf("New session cookie saved: %s\n", sessionCookie.c_str());
      }

      // Add cookie to the request headers
      if (sessionCookie.length() > 0) {
        http.addHeader("Cookie", sessionCookie);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");
        // Serial.printf("Session cookie sent: %s\n", sessionCookie.c_str());
      }

      httpResponseCode = http.POST(httpRequestData);

      if (httpResponseCode > 0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
        String payload = http.getString();
        Serial.println(payload);
      } else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      // Free resources
      http.end();

    } else {
      Serial.println("Connection failed: " + serverHost);
      deepSleep();
    }

  } else {
    Serial.println("WiFi unavailable");
    deepSleep();
  }

  digitalWrite(LED, HIGH);
  digitalWrite(PWR, LOW);
  deepSleep();
}


// Helper function for saving user entered settings
bool putValue(const char *target, String value) {
    if (value.length() > 0) {
        prefs.putString(target, value);
        return true;
    }
    return false;
};


// Local webserver at port 80
void handleWebServer() {

  WebServer server(80);
  server.begin();
  String content;

  {
    server.on("/", [&]() {
      String htmlStations = scanWiFi();
      String ipStr = "<small>WiFi SSID of this device is: <strong>" + ap_ssid + "</strong><br> \
        Password: <strong>" + ap_pass + "</strong><br> \
        IP address: <strong>" + WiFi.softAPIP().toString() + "</strong></small>";

      content = html_header + html_index;
      content.replace("{{ htmlStations }}", htmlStations);
      content.replace("{{ htmlContent }}", ipStr);
      server.send(200, "text/html", content);
    });

    server.on("/setting", [&]() {

      prefs.begin("sensor", RW_MODE);
      bool changed = false;

      changed |= putValue("wifiSsid", server.arg("ssid"));
      changed |= putValue("wifiPass", server.arg("pass"));
      changed |= putValue("serverHost", server.arg("host"));
      changed |= putValue("serverPath", server.arg("path"));
      changed |= putValue("pinCode", server.arg("pin"));

      if (server.arg("sleep").length() > 0) {
          prefs.putInt("sleepTime", server.arg("sleep").toInt());
          changed = true;
      }

      prefs.end();

      int statusCode;
      if (changed) {
        statusCode = 200;
        content = html_header + html_blank;
        content.replace("{{ htmlContent }}", "<h1>Success!</h1><p>Settings have been saved. Restarting the sensor.</p>");
        server.send(statusCode, "text/html", content);
        delay(1000);
        ESP.restart();
      } else {
        statusCode = 200;
        content = html_header + html_blank;
        content.replace("{{ htmlContent }}", "<h1>Error</h1><p>No settings changed.</p>");
        server.send(statusCode, "text/html", content);
      }
    });

    server.onNotFound([&]() {
      content = html_header + html_blank;
      content.replace("{{ htmlContent }}", "<h1>Error 404</h1><p>Not found.</p>");
      server.send(404, "text/html", content);
    });
  }

  // Blink LED while webserver is active
  while (true) {
    digitalWrite(LED, HIGH);
    delay(100);
    digitalWrite(LED, LOW);
    delay(100);
    server.handleClient();
  }
}


// Testing if WiFi available
bool testWiFi() {
  int i = 0;
  Serial.print("Testing WiFi...");
  while (i < 20) {
    if (WiFi.status() == WL_CONNECTED) {
      Serial.println(" connected!");
      return true;
    }
    delay(500);
    Serial.print(".");
    i++;
  }
  Serial.println(" not connected.");
  return false;
}

// Scan for available networks
String scanWiFi() {
  Serial.println("Scanning WiFi...");
  int n = WiFi.scanNetworks();

  Serial.println("#  SSID                       RSSI     SEC");
  Serial.println("------------------------------------------");

  String htmlStations;
  for (int i = 0; i < n; ++i) {
    String ssid = WiFi.SSID(i);
    int rssi = WiFi.RSSI(i);
    bool secured = WiFi.encryptionType(i) != WIFI_AUTH_OPEN;
    // Serial
    Serial.printf("%-2d ", i + 1);
    Serial.printf("%-24.24s ", ssid.c_str());
    Serial.printf("%4d dBm   ", rssi);
    Serial.println(secured ? "🔒" : " ");
    // html
    String rssiClass = "";
    if (rssi < -60)
      rssiClass = "medium";
    if (rssi < -70)
      rssiClass = "weak";
    if (rssi < -80)
      rssiClass = "poor";
    String securedClass = (secured ? "secured" : "");
    htmlStations += "<li class=\"" + rssiClass + "\"><span>" + WiFi.SSID(i) + "</span> <small>" + WiFi.RSSI(i) + " dBm <span class=\"" + securedClass + "\"></span></small></li>";
  }

  return htmlStations;
}


void deepSleep() {
  // Serial.println("sleep "+sleepTime);
  ESP.deepSleep((int)sleepTime * 1e6);  //Xe6 - X seconds
}

