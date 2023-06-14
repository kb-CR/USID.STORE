<?php
define('INI', '/');
define('ONT', '.');
define('ISI', ',');
define('ASI', ':');
define('ASP', ';');
define('EQL', '=');
define('EMT', ' ');

require('text/x-php/jsonld');

//echo getcwd();

$radix = new class(__FILE__) extends SplFileInfo {
    private ArrayObject $tense;
    private locale $locale;
    private route $route;
    private media $media;
    private identity $identity;

    public function php(SplFileInfo $file_info)
    {
        return include($file_info->getRealPath());
    }

    public function load(string $class_name)
    {
        $tense = mb_split(ISI, spl_autoload_extensions(), -1);
        if($tense !== false)
        {
            do
            {
                $extense = array_shift($tense);
                if(is_null($extense) === false)
                {
                    $file_name = $class_name . $extense;
                    $file_info = new SplFileInfo($file_name);
                    if($file_info->isFile())
                    {
                        $extension = $file_info->getExtension();
                        if(method_exists($this, $extension))
                        {
                            return $this->$extension($file_info);
                        }
                    }
                    else
                    {
                        if(empty($this->media))
                        {
                            $file_path = mime_content_type($this->getRealPath());
                        }
                        else
                        {
                            $file_path = $this->media->getMediaType($this->getFileInfo());
                        }
                        $file_info = new SplFileInfo(join(INI, [
                            $this->getPath(),
                            $file_path,
                            $file_name
                        ]));
                        if($file_info->isFile())
                        {
                            try
                            {
                                ob_start();
                                $file_object = $file_info->openFile('r', false, null);
                                $file_object->rewind();
                                $file_object->fpassthru();
                                $contents = ob_get_clean();
                                try
                                {
                                    $file_evaluation = eval($contents);
                                }
                                catch(Throwable $throwable)
                                {
                                    var_dump($throwable);
                                }
                                return $file_evaluation;
                            }
                            catch(Exception $exception)
                            {
                                return $exception;
                            }
                        }
                    }
                }
            }
            while(empty($tense) === false);
        }
    }

    public function register_autoload()
    {
        spl_autoload_register([
            $this,
            'load'
        ]);
    }

    public function register_extension(SplFileInfo $file_info)
    {
        // Let tense be a file loadings extensible name.
        $tense = $file_info->getExtension();
        // Assert unencountered extensible.
        if(isset($this->tense->$tense) === false)
        {
            // Let extension list be empty.
            spl_autoload_extensions('');
            // Let tense be extended uniquely.
            $this->tense->append($tense);
            // Let tense be iterated.
            $iterator = $this->tense->getIterator();
            // Be kind, rewind.
            $iterator->rewind();
            do
            {
                // Let tense be an item of a property.
                $tense = $iterator->current();
                // Let extense be a strung list of tense items.
                $extense = spl_autoload_extensions();
                // Let a tense item be strung infinitely.
                $extense = join(ISI . ONT, [
                    $extense,
                    $tense
                ]);
                // Assert an extense is strung.
                $iterator->next();
            }
            while($iterator->valid());
        }
    }

    public function __construct(string $file_name)
    {
        parent::__construct($file_name);
        $this->tense = new ArrayObject();
        $this->tense->setFlags(ArrayObject::ARRAY_AS_PROPS);
        $this->register_extension($this);
        $this->register_autoload();
        new configure('application/json/configure');
        $this->media = new media('application/json/media');
        $this->locale = new locale();
        $route = new route($this->media, new SplFileInfo($_SERVER['REQUEST_URI']));
        $language = new language('application/json/language');
        $identity = new identity();
        out::put();
        $route->out();;
        $language->getLanguages();
    }

    public function __destruct()
    {
        //out::get();
    }
};