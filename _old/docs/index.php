<!doctype html>
<html class="xivpads">
    <head>

    <!-- Default stuff -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, maximum-scale=1">
    <title>Viion - FFXIV Lodestone API (GitHub)</title>

    <!-- CDN -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900,500italic,500,700italic' rel='stylesheet' type='text/css'>
    <link href='http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.8.1/mustache.js"></script>

    <!-- Custom -->
    <link href='css/main.css' rel='stylesheet' type='text/css'>

    </head>
    <body>

        <header>
            <h1><i class="fa fa-code-fork"></i> FFXIV Lodestone API</h1>
            <h2>Documentation &nbsp;-&nbsp; <a href="https://github.com/viion/XIVPads-LodestoneAPI">GitHub: Viion "XIVPads-LodestoneAPI"</a></h2>
        </header>

        <nav> 
            <div class="box">
                <h3>misc</h3>
                <a href="?docs=home">Home</a>
                <a href="?docs=examples">Examples</a>
            </div>

            <div class="box">
                <h3>classes</h3>
                <a href="?docs=classes/api">API</a>
                <a href="?docs=classes/lodestone">Lodestone</a>
                <a href="?docs=classes/social">Social</a>
                <a href="?docs=classes/character">Character</a>
                <a href="?docs=classes/freecompany">FreeCompany</a>
                <a href="?docs=classes/linkshell">Linkshell</a>
                <a href="?docs=classes/achievements">Achievements</a>
                <a href="?docs=classes/parser">Parser</a>
            </div>

            <div class="box">
                <h3>traits</h3>
                <a href="?docs=traits/funky" class="green">Funky</a>
                <a href="?docs=traits/config" class="green">Config</a>
            </div>

            <div class="box">
                <h3>helpers</h3>
                <a href="?docs=helpers/logger" class="orange">Logger</a>
            </div>
        </nav>

        <main>
        <?php

            function printTable($table)
            {
                foreach($table as $dt)
                {
                    if (is_array($dt))
                    {
                        echo '<tr>
                            <td class="title">'. $dt[0] .'</td>
                            <td class="type">'. $dt[1] .'</td>
                            <td>'. $dt[2] .'</td>
                        </tr>';
                    }
                    else
                    {
                        echo '<tr class="tablesplit">
                            <td colspan="3"><h3><i class="fa fa-code"></i> &nbsp;&nbsp; '. $dt .'</h3></td>
                        </tr>';
                    }
                }
            }

            // Get the doc
            $doc = isset($_GET['docs']) ? trim($_GET['docs']) : null;

            // Documentation pages
            $pages =
            [
                'home'          => 'api/home.php',
                'classes/api'   => 'api/classes/api.php',
            ];

            // Page to include
            $pageToInclude = $pages['home'];
            if ($doc && isset($pages[$doc]))
            {
                $pageToInclude = $pages[$doc];
            }

            // Include it
            if ($pageToInclude)
            {
                include $pageToInclude;
            }
            else
            {
                echo '<div class="e404"><h1>Page not found!</h1><h2>Either still being created or does not exist</h2></div>';
            }

        ?>
        </main>

        <footer>
            Lodestone API by @Viion
        </footer>

    </body>
</html>