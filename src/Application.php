<?php

namespace TicketQueue\Server;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;
use TicketQueue\Server\Storage\LinkORBStorage;
use Symfony\Component\Yaml\Parser as YamlParser;
use LinkORB\Component\DatabaseManager\DatabaseManager;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use TicketQueue\Server\Security\CustomPasswordEncoder;

//use SuperBase\Component\Security\ApiKeyUserProvider as SuperBaseApiKeyUserProvider;
use TicketQueue\Server\Security\UserProvider as TicketQueueUserProvider;


use Pdo;

class Application extends SilexApplication
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->configureParameters();
        $this->configureApplication();
        $this->configureProviders();
        $this->configureServices();
        $this->configureSecurity();
        $this->configureListeners();
    }
    
    private function configureParameters()
    {
        
        $this['debug'] = true;

        $this['ticketqueue.server.baseurl'] = 'http://localhost:9321/';
        $this['ticketqueue.configfilename'] = __DIR__ . '/../config.yml';
    }
    
    private function configureApplication()
    {
        $parser = new YamlParser();
        $config = $parser->parse(file_get_contents($this['ticketqueue.configfilename']));
        foreach ($config as $key => $value) {
            $this[$key] = (string)$value;
        }
    }
    
    private function configureProviders()
    {
        // *** Setup Translation ***
        $this->register(new \Silex\Provider\LocaleServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());
        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'translator.messages' => array(),
        ));
        // *** Setup Form ***
        $this->register(new \Silex\Provider\FormServiceProvider());
        
        // *** Setup Twig ***
        $this->register(new \Silex\Provider\TwigServiceProvider());
        
        $options = array();
        $loader = null; // TODO
        $twig = new \Twig_Environment($loader, $options);
        $this['twig.loader.filesystem']->addPath(__DIR__ . '/../app/Resources/views', 'App');
        $this['twig.loader.filesystem']->addPath(__DIR__ . '/../src/Resources/views/dashboard', 'Dashboard');

        // *** Setup Sessions ***
        $this->register(new \Silex\Provider\SessionServiceProvider(), array(
            'session.storage.save_path' => '/tmp/ticketqueue_server_sessions'
        ));

        // *** Setup Routing ***
        $this->register(new \Silex\Provider\RoutingServiceProvider());

        // *** Setup Doctrine DBAL ***
        /*
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_mysql',
                    'host'      => $this['db.config.server'],
                    'dbname'    => $this['db.config.name'],
                    'user'      => $this['db.config.username'],
                    'password'  => $this['db.config.password'],
                    'charset'   => 'utf8',
            ),
        ));
        */

        // *** Setup Doctrine ORM ***
        /*
        $this->register(new DoctrineOrmServiceProvider, array(
            "orm.proxies_dir" => "/path/to/proxies",
            "orm.em.options" => array(
                "mappings" => array(
                    array(
                        "type" => "annotation",
                        "namespace" => "SuperBase\Entities",
                        "path" => __DIR__."/../../src/ApiRegistry/Entities",
                    )
                ),
            ),
        ));
        */
    }
    
    private function configureServices()
    {
        $gravatar = new \thomaswelton\GravatarLib\Gravatar();
        // example: setting default image and maximum size
        $gravatar->setDefaultImage('retro')
            ->setAvatarSize(150);
        
        switch ($this['storage.type']) {
            case 'linkorb':
                $dbname = $this['storage.dbname'];
                $manager = new DatabaseManager();

                $databaseconfig = $manager->getDatabaseConfigByDatabaseName($dbname);
                $storage = new LinkORBStorage($manager, $dbname, $gravatar);
                $this['ticketqueue.storage'] = $storage;
                
                break;
                
            case 'pdo':
            
                $manager = new DatabaseManager();
                
                
                $connectionstring = 'mysql:host=' . $this['storage.hostname'] . ';port=3306;dbname=' . $this['storage.dbname'];
                $pdo = new PDO(
                    $connectionstring,
                    $this['storage.username'],
                    $this['storage.password']
                );
                $storage = new PdoStorage($pdo);
                $this['ticketqueue.storage'] = $storage;
        }
    }
    
    private function configureSecurity()
    {
        $this->register(new \Silex\Provider\SecurityServiceProvider(), array());

        $manager = new DatabaseManager();

        $accountdbname = 'network';
        
        $accountdbal = $manager->getDbalConnection($accountdbname, 'default');
        //$this['security.encoder.digest'] = new PlaintextPasswordEncoder(true);
        //$this['security.encoder.digest'] = new CustomPasswordEncoder();
        //$userprovider = new TicketQueueDUserProvider($accountdbal);
        $userprovider = new \TicketQueue\Server\Security\JsonFileUserProvider('/share/config/user/');

        $this['security.firewalls'] = array(
            /*
            'api' => array(
                'anonymous' => false,
                'http' => true,
                'pattern' => '^/api',
                'users' => new TicketQueueApiKeyUserProvider($this['db'], null)
            ),
            */
            'dashboard' => array(
                'anonymous' => false,
                'pattern' => '^/dashboard',
                'form' => array('login_path' => '/login', 'check_path' => '/dashboard/login_check'),
                'logout' => array('logout_path' => '/dashboard/logout'),
                'users' => $userprovider,
            )
        );
    }
    
    private function configureListeners()
    {
        // todo
    }
}
