const String html_header = R"=====(
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>Connect to WiFi
</title>
<meta name="MobileOptimized" content="320"/>
<meta name="HandheldFriendly" content="true"/>
<meta name="viewport" content="initial-scale=1">
<meta name="theme-color" content="#377afc"/>
<style>
:root {
	--bg-main: #fff;
	--bg-index: #377afc linear-gradient(160deg, #377afc, #27eee7);
	--accent: #329cf6;
	--border-radius: 0.5rem;
	--fg-main: #000;
  --fg-button: var(--bg-main);
}
@media (prefers-color-scheme: dark) {
  :root {
	  --bg-main: #333;
	  --bg-index: #0000b8 linear-gradient(160deg, #0000b8, #00a7a5);
	  --accent: #0060b4;
  	--fg-main: #eee;
    --fg-button: #fff;
  }
  li::before,
  li small {
    filter: contrast(0) brightness(1.8);
  }
}
* { 
  box-sizing: border-box;  
} 
html { 
  font-family: sans-serif;
  background: var(--bg-main);
  color: var(--fg-main);
	-webkit-tap-highlight-color: transparent;
} 
body {
  margin: 0;
}
header,
main {
  padding: 1.5em 20%;
  @media all and (max-width: 767px)  { 
    padding: 1.5em;
  }
}
header {
  background: var(--bg-index);
  padding: 0.1px 0 6rem 0;
  margin-bottom: -8rem;
}

h1, h2 {
  font-weight: normal;
  margin: 1.5em 0 1em 0;
} 

header h1 {
  text-align: center;
  color: #fff;
}
header h1 a {
  display: block;
  text-align: center;
  margin: 6rem 0;
  &::after {
    content: "";
    text-decoration: none;
    display: inline-block;
    vertical-align: middle;
    width: 8rem;
    height: 8rem;
    border-radius: 50%;
    -webkit-tap-highlight-color: transparent;
    box-shadow: 0 0 0 2rem rgba(255, 255, 255, 0.1) ;
    outline: rgba(255, 255, 255, 0.1) 4rem solid;
    background-color: rgba(255, 255, 255, 0.4);
    background-image: url("data:image/svg+xml,%3Csvg width='48' height='48' version='1.1' viewBox='0 0 12.7 12.7' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.0583'%3E%3Cpath d='m7.673 5.2918h2.1167v-2.1167' style='paint-order:markers stroke fill'/%3E%3Cpath d='m9.3431 7.4093a3.175 3.175 0 0 1-3.4741 2.0791 3.175 3.175 0 0 1-2.692-3.0241 3.175 3.175 0 0 1 2.4675-3.2099 3.175 3.175 0 0 1 3.6146 1.8239' style='paint-order:markers stroke fill'/%3E%3C/g%3E%3C/svg%3E");
    background-position: center center;
    background-repeat: no-repeat;
    background-size: 4em;
    transition:
      background-size .5s cubic-bezier(0.25, 0.1, 0.25, 1.4),
      box-shadow .5s .1s cubic-bezier(0.25, 0.1, 0.25, 2.4),
      outline .5s .2s cubic-bezier(0.25, 0.1, 0.25, 2.4);
    @starting-style {
      background-size: 3em;
      box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.1);
      outline: rgba(255, 255, 255, 0.1) 0 solid;
    }
  }
}

header h1 a:hover:after {
    transition-delay: 0;
    background-size: 4.5em;
    box-shadow: 0 0 0 2.5rem rgba(255, 255, 255, 0.1);
    outline: rgba(255, 255, 255, 0.1) 5rem solid;
  }

ul { 
  border: var(--accent) 1px solid;
  border-radius: var(--border-radius);
  background: var(--bg-main);
  padding:0 1em;
} 
li { 
  list-style: none;
  margin: 0;
  padding: 1em 0;
  border-top: var(--accent) 1px solid;

}
li::before {
  content: "";
  display: inline-block;
  width: 2em;
  height: 1.2em;
  vertical-align: middle;
  background-image: url("data:image/svg+xml,%3Csvg width='48' height='48' version='1.1' viewBox='0 0 12.7 12.7' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='6.35' cy='9.525' r='1.323' style='paint-order:markers stroke fill'/%3E%3Cg fill='none' stroke='%23000' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.0583'%3E%3Cpath d='m8.7132 7.8264c-0.54677-0.76074-1.4263-1.2118-2.3632-1.2118-0.93685 4.96e-5 -1.8164 0.45107-2.3632 1.2118' style='paint-order:markers stroke fill'/%3E%3Cpath d='m10.114 6.1924c-0.95413-1.0775-2.3243-1.6944-3.7636-1.6945-1.4393 2.33e-5 -2.8095 0.61693-3.7636 1.6945' style='paint-order:markers stroke fill'/%3E%3Cpath d='m11.501 4.5744c-1.347-1.4013-3.2069-2.1933-5.1506-2.1931-1.9437-1.023e-4 -3.8036 0.79183-5.1506 2.1931' style='paint-order:markers stroke fill'/%3E%3C/g%3E%3C/svg%3E");
  background-position: left center;
  background-repeat: no-repeat;
  background-size: contain;
}
li.medium::before {
  background-image: url("data:image/svg+xml,%3Csvg width='48' height='48' version='1.1' viewBox='0 0 12.7 12.7' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='6.35' cy='9.525' r='1.323' style='paint-order:markers stroke fill'/%3E%3Cpath d='m8.7132 7.8264c-0.54677-0.76074-1.4263-1.2118-2.3632-1.2118-0.93685 4.96e-5 -1.8164 0.45107-2.3632 1.2118' fill='none' stroke='%23000' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.0583' style='paint-order:markers stroke fill'/%3E%3Cpath d='m10.114 6.1924c-0.95413-1.0775-2.3243-1.6944-3.7636-1.6945-1.4393 2.33e-5 -2.8095 0.61693-3.7636 1.6945' fill='none' stroke='%23000' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.0583' style='paint-order:markers stroke fill'/%3E%3C/svg%3E");
}
li.weak::before {
  background-image: url("data:image/svg+xml,%3Csvg width='48' height='48' version='1.1' viewBox='0 0 12.7 12.7' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='6.35' cy='9.525' r='1.323' style='paint-order:markers stroke fill'/%3E%3Cpath d='m8.7132 7.8264c-0.54677-0.76074-1.4263-1.2118-2.3632-1.2118-0.93685 4.96e-5 -1.8164 0.45107-2.3632 1.2118' fill='none' stroke='%23000' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.0583' style='paint-order:markers stroke fill'/%3E%3C/svg%3E");
}
li.poor::before {
  background-image: url("data:image/svg+xml,%3Csvg width='48' height='48' version='1.1' viewBox='0 0 12.7 12.7' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='6.35' cy='9.525' r='1.323' style='paint-order:markers stroke fill'/%3E%3C/svg%3E");
}
li:first-of-type {
  border: none;
}
li small { 
  float: right;
  opacity: 0.5;  
}
li small span { 
  display: inline-block;
  width:2.5rem;
  height: 0.8em;
} 
li small span.secured { 
  background-image: url("data:image/svg+xml,%3Csvg width='48' height='48' version='1.1' viewBox='0 0 12.7 12.7' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='m6.3495 1.8516c-1.3101 0-2.3807 1.0707-2.3807 2.3807v1.324c-0.43974 0-0.79375 0.35401-0.79375 0.79375v3.175c0 0.43974 0.35401 0.79375 0.79375 0.79375h4.7625c0.43974 0 0.79375-0.35401 0.79375-0.79375v-3.175c0-0.43974-0.35401-0.79375-0.79375-0.79375h-0.00103v-1.324c0-1.3101-1.0707-2.3807-2.3807-2.3807zm0 1.0583c0.74205 0 1.3245 0.58035 1.3245 1.3224v1.324h-2.6464v-1.324c0-0.74205 0.57983-1.3224 1.3219-1.3224z' style='paint-order:markers stroke fill'/%3E%3C/svg%3E");
  background-position: center center;
  background-repeat: no-repeat;
  background-size: 1rem;
} 
li small::after { 
  content: '';
  border: solid #000;
  border-width: 0 2px 2px 0;
  display: inline-block;
  padding: 4px;
  transform: rotate(-45deg);
  transition: transform 0.2s;  
} 

form { 
  margin: 2em 0;
  font-family: sans-serif;
  border-radius: var(--border-radius);

}
input {
  margin: 0.5rem 0;
  padding: 1em;
  font-size: 1rem;
  border: var(--accent) 1px solid;
  border-radius: var(--border-radius);
  background: var(--bg-main);
  color: var(--fg-main);
}
input:active,
input:focus {
  outline: var(--accent) 1px solid;
}
input[type=text],
input[type=number],
input[type=password] { 
  width:100%;  
} 
input[type=button],
input[type=submit] {
  background: var(--accent);
  color: var(--fg-button);
}

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const parentElement = document.querySelector("#list");
  parentElement.addEventListener("click", function (event) { 
    const currentTarget = event.target;
    text = currentTarget.closest("li").querySelector('span').textContent;
    document.getElementsByName("ssid")[0].value = text;
    document.getElementsByName("pass")[0].focus({ 
      preventScroll: true 
    });
    setTimeout(function() {
      document.getElementById("form").scrollIntoView({ 
        behavior: "smooth", block: "end", inline: "nearest"  
      });
    }, 300);
  });
  const urlInput = document.getElementsByName('url')[0];
  const hostInput = document.getElementsByName('host')[0];
  const pathInput = document.getElementsByName('path')[0];
  urlInput.addEventListener("change", (event) => {
    let url = new URL(event.target.value);
    let path = url.pathname + "/data_write.php";
    path = path.replace(/\/+/g, "/");
    hostInput.value = url.hostname;
    pathInput.value = path;
  });
});
</script>
</head>
)=====";

const String html_index = R"=====(
<body>
  <header>
    <h1>Connect to WiFi<a href="/"></a></h1>
  </header>
  <main>
    <ul id="list">
      {{ htmlStations }}
    </ul>

    <form id="form" method="get" action="/setting">
      <input type="text" name="ssid" placeholder="Wifi SSID" length="32">
      <input type="password" name="pass" placeholder="Wifi Password" length="64">
      <h2>Connect to server</h2>
      <input type="text" name="url" placeholder="Server URL" length="64">
      <h2>Configure the sensor</h2>
      <input type="password" name="pin" placeholder="PIN code from server" inputmode="numeric" maxlength="4">
      <input type="number" name="sleep" placeholder="Sleep time, seconds" inputmode="numeric" length="7" min="1" max="2592000">
      <input type="hidden" name="host">
      <input type="hidden" name="path">
      <input type="submit" id="submit" value="Connect">
    </form>
    <p>Settings can be changed later. To turn on pairing mode, reset the sensor twice within a second.</p>

    {{ htmlContent }}
  </main>
</body>
</html>
)=====";

const String html_blank = R"=====(
<body>
  <main>
    {{ htmlContent }}
  </main>
</body>
</html>
)=====";