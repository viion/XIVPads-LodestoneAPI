<?php
/*
 *
 *              THIS IS IN DEVELOPMENT
 *                    DO NOT USE
 *
 */
namespace Viion\Lodestone;
/*  --------------------------------------------------
 *  XIVPads.com (v5) - Lodestone API
 *  --------------------------------------------------
 *  Author:     Josh Freeman (Premium Virtue)
 *  Support:    http://xivpads.com/?Portal
 *  Version:    5
 *  PHP:        5.4
 *
 *  Always ensure you download from the github
 *  > https://github.com/viion/XIVPads-LodestoneAPI
 *  --------------------------------------------------
 *  Special Thanks: @eedev, @stygiansabyss
 *  --------------------------------------------------
 *  Note on foreign characters: These in raw format
 *  will show very weird, this happens when debugging
 *  and it is usually because no charset, you can add a
 *  meta tag to your document to get true output.
 *
 *      <meta charset="UTF-8">
 *
 *  Additionally you can do:
 *
 *      $API->setOutputToUTF8();
 *  --------------------------------------------------
 *  Coding Style:
 *      - Options WILL_BE_CAPITALIZED
 *      - Classes/Traits Will Start With A Capital
 *      - variables willBeCamelCase
 *      - Chain functioning is used where useful
 *      - If an if statement has a 1 line condition, 
 *        i wont use brackets
 *      - I comment a lot.
 *      - Log timestamp is seconds with 3 decimal ms
 *  --------------------------------------------------
 *  Legacy functions exist for the sake of not breaking
 *  peoples code! They will just call new methods.
 */

#----------------------------------------------------#
# Defines                                            #
#----------------------------------------------------#

// Default Options  
define('AUTOMATICALLY_PARSE_FREE_COMPANY_MEMBERS', true);

#----------------------------------------------------#
# Traits                                             #
#----------------------------------------------------#

/*  Trait: Urls
 *  - Urls of funness
 */
trait Urls
{
    public $LodestoneUrls =
    [
        'base' => 'http://eu.finalfantasyxiv.com/lodestone/',

        // Lodestone search
        'search' =>
        [
            'path'  => 'character',
            'query' => [ 'q', 'worldname' ],
        ],

        // Lodestone character profile
        'character'   =>
        [
            'path'  => 'character',
        ],

        // Lodestone free company
        'fc' =>
        [
            'path'  => 'freecompany',
            'members' =>
            [
                'path'  => 'member',
                'query' => ['page'],
            ],
        ],

        // Lodestone linkshell
        'ls' =>
        [
            'path' => 'linkshell',
            'activity' =>
            [
                'path' => 'activity',
            ]
        ],

        // Lodestone frontpage
        'frontpage' =>
        [
            'news'          => 'news',
            'topics'        => 'topics',
            'notices'       => 'news/category/1',
            'maintenance'   => 'news/category/2',
            'updates'       => 'news/category/3',
            'status'        => 'news/category/4',
        ],

        // Official forums
        'forums' => 
        [
            'index' => 'http://forum.square-enix.com/ffxiv/forum.php',
        ],

        // Youtube API
        'youtube' =>
        [
            'channels'      => 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername={channel}&key={key}',
            'playlists'     => 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults={max}&playlistId={playlistID}&key={key}',
        ],

        // Twitter
        'twitter' =>
        [
            'en' => 'https://twitter.com/ff_xiv_en',
        ],
    ];

    // Generates a url based on a type and some params (optional)
    private function generateUrl($type, $params = null)
    {
        if ($type == 'search')
        {
            // name + server
            $name   = trim(str_ireplace(' ', '+', $params[0]));
            $server = trim($params[1]);

            // path
            $path = 
            [
                $this->LodestoneUrls['base'],
                $this->LodestoneUrls['search']['path'],
            ];

            // query
            $query = 
            [
                $this->LodestoneUrls['search']['query'][0] .'='. $name,
                $this->LodestoneUrls['search']['query'][1] .'='. $server,
            ];

            // create url
            $url = implode('/', $path) .'?'. implode('&', $query);
        }

        return $url;
    }
}

/*  trait: Output
 *  - Provides functions for outputting to the screen.
 *    Mainly used in debugging.
 */
trait Output
{
    /*  show
     *  @param $data - string or array, if empty, will print $this
     *
     *  Simple function that wraps <pre> around print_r
     */
    public function show($data = false)
    {
        if ($data === false) 
        {
            $data = $this;
        }

        // Echo results
        echo '<pre>'. print_r($data, true) .'</pre>';
    }

    /*  utf8
     *  - Prints a meta tag so on screen characters
     *    will be utf8 compliant. Makes foreign characters
     *    not look so crazy.
     */
    public function setOutputToUTF8()
    {
        // Set utf8 charset
        echo '<meta charset="UTF-8">';
    }
}

/*  Trait: Functions
 *  - Various functions classes can use.
 */
trait Functions
{

}

/*  Trait: Traits
 *  - Gets all traits
 *
 *  ... trait trait traity trait.
 */
trait Traits
{
    use Urls;
    use Output;
    use Functions;
}

#----------------------------------------------------#
# Classes                                            #
#----------------------------------------------------#

/*  Class: API
 *  - The core API interaction
 */
class LodestoneAPI
{
    use Traits;

    // classes
    public $Log;
    public $Parser;
    public $Search;

    // -----------------------------------------------
    // init
    // -----------------------------------------------

    // __construct
    function __construct()
    {
        $this->Log = new APILogger();
        $this->Log->add(__LINE__, 'API Initialized.', APILogger::SUCCESS);

        // Setup API classes
        $this->Parser = new APIParser($this->Log);
        $this->Search = new APISearch($this->Log);




        
    }

    // __destruct
    function __destruct() {}

    // -----------------------------------------------
    // public
    // -----------------------------------------------

    // -----------------------------------------------
    // private
    // -----------------------------------------------
}

/*  Class: Search
 *  - Handles searching for things!
 */
Class APISearch
{
    use Traits;

    // classes
    public $Log;
    public $Parser;

    // vars
    private $useExactMatching = true;

    // -----------------------------------------------
    // init
    // -----------------------------------------------

    // __construct
    function __construct($Log)
    {
        $this->Log = $Log;
        $this->Parser = new APIParser($this->Log);
    }

    // -----------------------------------------------
    // public
    // -----------------------------------------------

    /*  Character
     *  - Search for a character
     *
     *  @param $name - string, character name
     *  @param $server - string, character server
     */
    public function Character($name, $server)
    {
        $this->Log->add(__LINE__, '[APISearch] New search for: '. $name .' - '. $server);

        // Generate url
        $url = $this->generateUrl('search', [$name, $server]);

        // Get source
        $this->Parser->get($url);
        $this->Parser->show();
    }


    /*  Toggle useExactMatching
     *
     *  @param $toggle - boolean on/off
     */
    public function useExactMatching($toggle)
    {
        $this->useExactMatching = $toggle;
    }

    // -----------------------------------------------
    // private
    // -----------------------------------------------
}

/*  Class: Parser
 *  - Parsing functions classes can use.
 */
Class APIParser
{
    use Traits { 
        Traits::show as traitShow; 
    }

    // classes
    private $Log;
    
    // vars
    private $sourceCode;
    private $sourceCodeUrl;
    private $sourceCodeArray;
    private $sourceCodeSize;

    function __construct($Log)
    {
        $this->Log = $Log;
    }

    // -----------------------------------------------
    // public
    // -----------------------------------------------

    // Get the source code of some url (returns as newline array)
    public function get($url)
    {
        $this->Log->add(__LINE__, '[Parser] Getting source for url: '. $url);

        // Check if url set
        if ($url) $this->sourceCodeUrl = $url;

        // Parse sourcode
        $this->sourceCode = $this->curl();
        $this->sourceCodeArray = explode("\n", $this->sourceCode);
        $this->sourceCodeSize = strlen($this->sourceCode);

        // Return
        return $this->sourceCodeArray;
    }

    // Get just the code of not some url
    public function getCode()
    {
        return $this->sourceCode;
    }

    // Get the size of the source code
    public function getSize()
    {
        return $this->sourceCodeSize;
    }

    // Prints the source array
    public function show()
    {
        $this->traitShow($this->sourceCodeArray);
    }

    // -----------------------------------------------
    // private
    // -----------------------------------------------

    private function curl()
    {
        $this->Log->add(__LINE__, '[Parser] START: curl('. $this->sourceCodeUrl .')');

        // Options for curl
        $options =
        [ 
            CURLOPT_RETURNTRANSFER  => true,            // return web page
            CURLOPT_HEADER          => false,           // return headers
            CURLOPT_FOLLOWLOCATION  => false,           // follow redirects
            CURLOPT_ENCODING        => "",              // handle all encodings
            CURLOPT_AUTOREFERER     => true,            // set referer on redirect
            CURLOPT_CONNECTTIMEOUT  => 15,              // timeout on connects
            CURLOPT_TIMEOUT         => 15,              // timeout on response
            CURLOPT_MAXREDIRS       => 5,               // stop after 10 redirects
            CURLOPT_USERAGENT       => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36", 
            CURLOPT_HTTPHEADER      => ['Content-type: text/html; charset=utf-8', 'Accept-Language: en'],
        ];
        
        // Init curl
        $ch = curl_init($this->sourceCodeUrl);  

        // Set Options
        curl_setopt_array($ch, $options);   

        // Get source
        $source = curl_exec($ch);

        // Close
        curl_close($ch);

        // Convert to html entities
        $html = htmlentities($source);

        // End
        $this->Log->add(__LINE__, '[Parser] FINISH: curl('. $this->sourceCodeUrl .')');
        return $html; 
    }
}

/*  Class: Log
 *  - A logging utility, makes debugging what is going on
 *    a bit easier and provides times between executions.
 */
class APILogger
{
    use Traits { 
        Traits::show as traitShow; 
    }

    // vars
    private $start;
    private $finish;
    private $duration;
    private $previous = false;
    private $messages = [];

    // const
    const WARNING = 1;
    const SUCCESS = 2;

    // -----------------------------------------------
    // public
    // -----------------------------------------------

    /*  add
     *  - Add to the log
     */
    public function add($line, $text, $type = 0)
    {
        // Get the delay from the previous entry
        $delay = '-';
        if ($this->previous !== false)
        {
            $delay = ($this->now() - $this->previous);
        }

        // Set the previous time to now
        $this->previous = $this->now();

        // Style delay
        $delay = $this->styleDelay($delay);

        // Colorize
        $text = $this->styleText($text, $type);

        // Log it
        $timeformat = substr_replace($this->now(), '-', -3, 0);
        $this->messages[$timeformat][] = '('. $delay .' ms) '. $text;
    }


    /*  show
     *  - Show the log messages
     */
    public function show()
    {
        // If no messages, return right away
        if (empty($this->messages))
        {
            return false;
        }
        
        // Calculate times
        $this->calculate();

        // ksort
        ksort($this->messages);
        $this->traitShow($this->messages);
    }

    // -----------------------------------------------
    // private
    // -----------------------------------------------

    // Color the delay
    private function styleDelay($delay)
    {
        $html = '{{delay}}';

        if ($delay > 1000) $html = '<span style="background-color:#f00;color:#fff;font-weight:bold;border-radius:3px;">{{delay}}</span>';
        else if ($delay > 250) $html = '<span style="background-color:#AC7206;color:#fff;font-weight:bold;border-radius:3px;">{{delay}}</span>';
        else if ($delay > 100) $html = '<span style="background-color:#8EC018;color:#fff;font-weight:bold;border-radius:3px;">{{delay}}</span>';
        else if ($delay < 10) $html = '<span style="color:#5CA21E;font-weight:bold;">{{delay}}</span>';

        $html = str_ireplace('{{delay}}', $delay, $html);

        return $html;
    }

    private function styleText($text, $type)
    {
        $html = '{{text}}';

        if ($type == self::WARNING) $html = '<span style="background-color:#f00;color:#fff;font-weight:bold;border-radius:3px;">{{text}}</span>';
        else if ($type == self::SUCCESS) $html = '<span style="background-color:#74C100;color:#fff;font-weight:bold;border-radius:3px;">{{text}}</span>';

        $html = str_ireplace('{{text}}', $text, $html);

        return $html;
    }

    // calculate: will set start, finish and duration based on log
    private function calculate()
    {
        // Sort it
        ksort($this->messages);

        // Get start
        reset($this->messages);
        $this->start = str_ireplace('-', null, key($this->messages));
        
        // Get finish
        end($this->messages);
        $this->finish = str_ireplace('-', null, key($this->messages));

        // Get duration
        $this->duration = $this->finish - $this->start;

        // Format it to be more readable
        if ($this->duration > 0)
        {
            $this->duration = round(($this->duration / 1000), 5);
        }
    }

    // Get the time now
    private function now()
    {
        $time = round(microtime(true) * 1000);
        return $time;
    }
}

#----------------------------------------------------#
# Legacy                                             #
#----------------------------------------------------#

class_alias('Viion\Lodestone\LodestoneAPI', '_Shared\Lodestone\API');
class_alias('Viion\Lodestone\LodestoneAPI', 'LodestoneAPI');