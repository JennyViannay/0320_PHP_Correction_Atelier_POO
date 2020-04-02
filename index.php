<?php
// J'ajoute le namespace des classes pour pouvoir les utilisers sur mon index.php
namespace Animals;
// J'ajoute mon USE PDO
use \PDO;
use \PDOException;
// session
session_start();
// Et mon require du fichier connect
// Ainsi que les require des fichiers de class CAT & COLLAR
require 'classes/Cat.php';
require 'classes/Collar.php';
require 'config/connect.php';
// CONNECT BDD
$pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
if ($pdo === false) {
    echo 'Connection Error :' . $pdo->error_log();
}


// Fast debug function -> pour debuguer plus vite
function bug($var)
{
    var_dump($var);
}

// Instancier un nouveau collier pour pouvoir le passer en 3eme paramètre du __construc cat si besoin
$foo = new Collar('small', 'red');

// Instancier un nouveau cat
$plop = new Cat('name', 'color');

// Set le niveau de fatigue des cats :
$plop->setTiredness(90);

// Faire manger les cats
$plop->eat();

// Faire marcher les cats jusqu'à qu'ils soient fatigués (fatigue max = 100)
for ($i = $plop->getTiredness(); $i < 101; $i++) {
    $plop->walk();
}

// Faire reposer les cats dès que leur niveau de fatigue atteint 100 
if ($plop->getTiredness() === 100) {
    $plop->rest();
}

// Afficher les valeurs de cat 
// bug($plop);


//-----------------------------------------------------------------------------------------------------//
//-------------------------------------GAME KITTEN & BULLSHIT------------------------------------------//
//-----------------------------------------------------------------------------------------------------//

// CREATE KITTEN 
if (isset($_GET) && isset($_GET['new_cat'])) {
    if (!empty($_GET['name']) && !empty($_GET['colors']) && !empty($_GET['img'])) {
        $idCollar = "";
        if (!empty($_GET['colors_collar']) && !empty($_GET['size_collar'])) {
            $collar = new Collar($_GET['size_collar'], $_GET['colors_collar']);
            try {
                $request = $pdo->prepare("INSERT INTO collar (size, color) VALUE (:size, :color)");
                $request->execute([
                    'size' => $collar->getSize(),
                    'color' => $collar->getColor()
                ]);
                $idCollar = $pdo->lastInsertId();
            } catch (PDOException $e) {
                echo $error = $e->getMessage();
            }
        } else {
            echo "Définir un collier";
        }
        $cat = new Cat($_GET['name'], $_GET['colors']);
        $cat->setImage($_GET['img']);
        $cat->setCollar($collar);
        try {
            $request = "SELECT * FROM collar WHERE id=" . $idCollar;
            $sendRequest = $pdo->query($request);
            if ($sendRequest === false) {
                $pdo->errorInfo();
            }
            $_COLLAR = $sendRequest->fetchObject();
            $request = $pdo->prepare(
                "INSERT INTO cat (name, color, tiredness, image, collar_size, collar_color) 
                 VALUES (:name, :color, :tiredness, :image, :collar_size, :collar_color);"
            );
            $request->execute([
                'name' => $cat->getName(),
                'color' => $cat->getColor(),
                'tiredness' => $cat->getTiredness(),
                'image' => $cat->getImage(),
                'collar_size' => $_COLLAR->size,
                'collar_color' => $_COLLAR->color
            ]);
            $id = $pdo->lastInsertId();
            $_SESSION['isConnected'] = true;
            header('Location: http://localhost:8000/?id=' . $id);
        } catch (PDOException $e) {
            echo "ligne 94 :" . $error = $e->getMessage();
        }
    }
}

if (isset($_GET) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $request = "SELECT * FROM cat WHERE id=" . $id;
    $sendRequest = $pdo->query($request);
    if ($sendRequest === false) {
        $pdo->errorInfo();
    }
    $_CAT = $sendRequest->fetchObject();
}

if (isset($_POST) && !empty($_POST['get_play'])) {
    $id = $_GET['id'];
    $tiredness = $_CAT->tiredness + 10;
    var_dump($tiredness);
    try {
        $request = $pdo->prepare("UPDATE cat SET tiredness=:tiredness WHERE id=:id");
        $request->execute([
            'tiredness' => $tiredness,
            'id' => $id
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST) && !empty($_POST['get_eat'])) {
    $id = $_GET['id'];
    $tiredness = $_CAT->tiredness - 10;
    var_dump($tiredness);
    try {
        $request = $pdo->prepare("UPDATE cat SET tiredness=:tiredness WHERE id=:id");
        $request->execute([
            'tiredness' => $tiredness,
            'id' => $id
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST) && !empty($_POST['get_sleep'])) {
    $id = $_GET['id'];
    $tiredness = $_CAT->tiredness - 10;
    var_dump($tiredness);
    try {
        $request = $pdo->prepare("UPDATE cat SET tiredness=:tiredness WHERE id=:id");
        $request->execute([
            'tiredness' => $tiredness,
            'id' => $id
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST) && !empty($_POST['get_kill'])) {
    $id = $_GET['id'];
    $tiredness = $_CAT->tiredness - 10;
    var_dump($tiredness);
    try {
        $request = $pdo->prepare("DELETE FROM cat where id=:id");
        $request->execute(['id' => $id]);
        $request = $pdo->prepare("DELETE FROM collar where id=:id");
        $request->execute(['id' => $_COLLAR->id]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    session_destroy();
    header('Location: http://localhost:8000');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="description" content="NES.css is a NES-style CSS Framework." />
    <meta name="keywords" content="html5,css,framework,sass,NES,8bit" />
    <meta name="author" content="© 2018 B.C.Rikko" />
    <meta name="theme-color" content="#212529" />
    <link rel="shortcut icon" type="image/png" href="favicon.png">
    <link rel="shortcut icon" sizes="196x196" href="favicon.png">
    <link rel="apple-touch-icon" href="favicon.png">

    <title>Kitten & Bullshit</title>

    <link href="https://unpkg.com/nes.css@2.2.1/css/nes.min.css" rel="stylesheet" />
    <link href="assets/style.css" rel="stylesheet" />
    <script src="lib/vue.min.js"></script>
    <script src="lib/dialog-polyfill.js"></script>
    <script src="lib/highlight.js"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-41640153-4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag("js", new Date());
        gtag("config", "UA-41640153-4");
    </script>
    <script>
        if (window.navigator.userAgent.toLocaleLowerCase().indexOf('trident') !== -1) {
            window.alert('IE is not supported on this page.')
        }
    </script>
</head>

<body>
    <div id="nescss">
        <div class="container">
            <main class="main-content">
                <?php if (isset($_CAT) && !empty($_CAT)) { ?>
                    <a class="github-link" :class="{ active:  scrollPos < 300 }" href="" rel="noopener" @mouseover="startAnimate" @mouseout="stopAnimate">
                        <p class="nes-balloon from-right">
                            Salut je suis <?= $_CAT->name ?> !
                        </p>
                        <i class="nes-octocat" :class="animateOctocat ? 'animate' : ''"></i>
                    </a>
                <?php } ?>
                <?php if (!isset($_CAT)) { ?>
                    <!-- Create a Kitten -->
                    <section class="topic">
                        <h2 id="about"><a href="#about">#</a>Create Kitten :</h2>
                        <form method="GET">
                            <input class="nes-input" type="text" placeholder="Name" name="name" required> 
                            <br> <br>
                            <div class="nes-select">
                                <select required id="default_select" name="colors">
                                    <option value="" disabled="" selected="" hidden="">Select color</option>
                                    <option value="ecaille">ecaille</option>
                                    <option value="mistigris">mistigris</option>
                                    <option value="blanc">blanc</option>
                                    <option value="noir">noir</option>
                                    <option value="roux">roux</option>
                                </select>
                            </div>
                            <br>
                            <input class="nes-input" type="text" placeholder="url img" name="img" required> 
                            <br> <br>
                            <div class="nes-select">
                                <select required id="default_select" name="size_collar">
                                    <option value="" disabled="" selected="" hidden="">Select collar size</option>
                                    <option value="small">small</option>
                                    <option value="medium">medium</option>
                                    <option value="large">large</option>
                                </select>
                            </div>
                            <br>
                            <div class="nes-select">
                                <select required id="default_select" name="colors_collar">
                                    <option value="" disabled="" selected="" hidden="">Select collar color</option>
                                    <option value="rouge">rouge</option>
                                    <option value="vert">vert</option>
                                    <option value="jaune">jaune</option>
                                    <option value="violet">violet</option>
                                </select>
                            </div>
                            <br>
                            <button type="submit" class="nes-btn is-primary" name="new_cat">Create</button>
                        </form>
                    </section>
                <?php } ?>

                <!-- style="overflow-y: scroll; height:300px;" -->
                <?php if (isset($_SESSION['isConnected']) && $_SESSION['isConnected'] == true) { ?>
                    <section class="nes-container ">
                        <section class="message-list">
                            <section class="message -left">
                                <i class="nes-octocat animate"></i>
                                <?php if ($_CAT->tiredness < 40) { ?>
                                    <div class="nes-balloon from-left is-dark">
                                        <p> Salut je suis plein d'energie ! Fais moi jouer ! </p>
                                    </div>
                                <?php } ?>
                                <?php if ($_CAT->tiredness >= 40 && $_CAT->tiredness < 65) { ?>
                                    <div class="nes-balloon from-left is-dark">
                                        <p> J'ai un peu faim maintenant ! </p>
                                    </div>
                                <?php } ?>
                                <?php if ($_CAT->tiredness >= 65 &&  $_CAT->tiredness < 80) { ?>
                                    <div class="nes-balloon from-left is-dark">
                                        <p> J'ai sommeil ! </p>
                                    </div>
                                <?php } ?>
                                <?php if ($_CAT->tiredness >= 80 &&  $_CAT->tiredness < 100) { ?>
                                    <div class="nes-balloon from-left is-dark">
                                        <p> J'ai faim et j'ai sommeil, fais gaffe à ta gueule ! </p>
                                    </div>
                                <?php } ?>
                                <?php if ($_CAT->tiredness >= 100 && $_CAT->tiredness < 150) { ?>
                                    <div class="nes-balloon from-left is-dark">
                                        <p> Je me sens pas bien du tout xx </p>
                                    </div>
                                <?php } ?>
                                <?php if ($_CAT->tiredness >= 150) { ?>
                                    <div class="nes-balloon from-left is-dark">
                                        <p> Ca y est je suis dead xx </p>
                                    </div>
                                <?php } ?>
                            </section>
                            <section class="message -right"></section>
                        </section>
                        <section class="nes-container with-title">
                            <h3 class="title">Actions</h3>
                            <div id="buttons" class="item">
                                <form method="POST">
                                    <button type="submit" class="nes-btn is-primary" name="get_play" value="play">Play</a>
                                    <button type="submit" class="nes-btn is-success" name="get_eat" value="eat">Eat</a>
                                    <button type="submit" class="nes-btn is-warning" name="get_sleep" value="sleep">Sleep</a>
                                    <button type="submit" class="nes-btn is-error" name="get_kill" value="kill">Kill</a>
                                </form>
                            </div>
                        </section>
                <?php } ?>
            </main>
        </div>
</body>
<script src="assets/script.js"></script>
<script>
    const h = document.querySelector('head');
    ['./lib/dialog-polyfill.css', './lib/highlight-theme.css'].forEach(a => {
        const l = document.createElement('link');
        l.href = a;
        l.rel = 'stylesheet';
        h.appendChild(l);
    })
</script>

</html>