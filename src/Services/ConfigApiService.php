<?php namespace Usulix\NetSuite\Services;

class ConfigApiService
{

    protected $booConfigOk;
    protected $logger;
    protected $arrTokenFields = [
        'NETSUITE_RESTLET_HOST',
        'NETSUITE_ACCOUNT',
        'NETSUITE_CONSUMER_KEY',
        'NETSUITE_CONSUMER_SECRET',
        'NETSUITE_TOKEN',
        'NETSUITE_TOKEN_SECRET'
    ];
    protected $arrNlAuthFields = [
        'NETSUITE_RESTLET_HOST',
        'NETSUITE_ACCOUNT',
        'NETSUITE_EMAIL',
        'NETSUITE_PASSWORD',
        'NETSUITE_ROLE'
    ];

    public function __construct($log)
    {
        $this->booConfigOk = false;
        $this->logger = $log;

        if ($this->checkFields('arrTokenFields') || $this->checkFields('arrNlAuthFields')) {
            $this->booConfigOk = true;
        }

        return $this->booConfigOk;
    }

    public function checkFields($strType)
    {
        foreach ($this->$strType as $strField) {
            if (!$this->getFromConfig($strField)) {
                return false;
            }
        }

        return true;
    }

    public function getConfig()
    {
        if (!$this->booConfigOk) {
            $this->logger->info('Config settings for netsuite service appear to be incomplete');

            return false;
        }
        if ($this->getFromConfig('NETSUITE_PASSWORD')) {
            $arrConfig = [
                'host'     => $this->getFromConfig('NETSUITE_RESTLET_HOST'),
                'account'  => $this->getFromConfig('NETSUITE_ACCOUNT'),
                'email'    => $this->getFromConfig('NETSUITE_EMAIL'),
                'password' => $this->getFromConfig('NETSUITE_PASSWORD'),
                'role'     => $this->getFromConfig('NETSUITE_ROLE'),
            ];
        } else {
            $arrConfig = [
                'host'           => $this->getFromConfig('NETSUITE_RESTLET_HOST'),
                'account'        => $this->getFromConfig('NETSUITE_ACCOUNT'),
                'consumerKey'    => $this->getFromConfig('NETSUITE_CONSUMER_KEY'),
                'consumerSecret' => $this->getFromConfig('NETSUITE_CONSUMER_SECRET'),
                'token'          => $this->getFromConfig('NETSUITE_TOKEN'),
                'tokenSecret'    => $this->getFromConfig('NETSUITE_TOKEN_SECRET')
            ];
            if ($this->getFromConfig('NETSUITE_SIGNATURE_ALGORITHM')) {
                $arrConfig['signatureAlgorithm'] = $this->getFromConfig('NETSUITE_SIGNATURE_ALGORITHM');
            } else {
                $arrConfig['signatureAlgorithm'] = 'HMAC-SHA256';
            }
        }
        if ($this->getFromConfig('NETSUITE_LOGGING')) {
            $arrConfig['logging'] = $this->getFromConfig('NETSUITE_LOGGING');
        }
        if ($this->getFromConfig('NETSUITE_LOG_PATH')) {
            $arrConfig['log_path'] = $this->getFromConfig('NETSUITE_LOG_PATH');
        }

        return $arrConfig;
    }

    public function getFromConfig($key)
    {
        $config = config('services.netsuite.default', getenv('NETSUITE_ENVIRONMENT', 'sandbox'));
        $nsConfig = 'services.netsuite.' . $config . '.';
        $map = [
            'NETSUITE_ENDPOINT' => config($nsConfig . 'endpoint', '2017_1'),
            'NETSUITE_WEBSERVICES_HOST' => config($nsConfig . 'webservices', 'https://webservices.sandbox.netsuite.com'),
            'NETSUITE_RESTLET_HOST' => config($nsConfig . 'url'),
            'NETSUITE_ACCOUNT' => config($nsConfig . 'accountNumber'),
            'NETSUITE_CONSUMER_KEY' => config($nsConfig . 'consumerKey'),
            'NETSUITE_CONSUMER_SECRET' => config($nsConfig . 'consumerSecret'),
            'NETSUITE_TOKEN' => config($nsConfig . 'token'),
            'NETSUITE_TOKEN_SECRET' => config($nsConfig . 'tokenSecret'),
            'NETSUITE_EMAIL' => config($nsConfig . 'email'),
            'NETSUITE_PASSWORD' => config($nsConfig . 'password'),
            'NETSUITE_ROLE' => config($nsConfig . 'role'),
            'NETSUITE_SIGNATURE_ALGORITHM' => config($nsConfig . 'signatureAlgorithm'),
            'NETSUITE_LOGGING' => config($nsConfig . 'logging'),
            'NETSUITE_LOG_PATH' => config($nsConfig . 'logPath'),
        ];

        return $map[$key] ?? getenv($key) ?? null;
    }
}
