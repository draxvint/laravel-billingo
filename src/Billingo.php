<?php

namespace Polynar\Billingo;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Polynar\Billingo\Traits\ProcessErrorsTrait;
use Swagger\Client\Api\DocumentApi;
use Swagger\Client\Api\PartnerApi;
use Swagger\Client\Configuration as SwaggerConfig;


class Billingo
{
    /**
     * Download files path
     *
     * @var string
     */
    protected $downloadPath = 'invoices/';

    /**
     * Downloaded file extension
     *
     * @var string
     */
    protected $extension = '.pdf';

    protected $httpClient;

    /**
     * Delete the invoice
     *
     * @param integer $id
     *
     * @return self
     */
    public function cancelInvoice(int $id): self
    {
        $this->createResponse('cancel', [$id], true);

        return $this;
    }

    /**
     * Check valid tax number
     *
     * @param string $tax_number
     *
     * @return self
     */
    public function checkTaxNumber(string $taxNumber): self
    {
        $this->createResponse('checkTaxNumber', [$taxNumber]);

        return $this;
    }

    /**
     * Call create$apiName method
     *
     * @throws Exception (methodExists)
     * @return self
     */
    public function create(): self
    {
        $this->createResponse('create', [$this->model], true);

        return $this;
    }

    /**
     * Create invoice from proforma
     *
     * @param integer $id
     *
     * @return self
     */
    public function createInvoiceFromProforma(int $id): self
    {
        $this->createResponse('createDocumentFromProforma', [$id]);

        return $this;
    }

    /**
     * Delete delete$apiName method
     *
     * @param integer $id
     *
     * @return self
     */
    public function delete(int $id): self
    {
        $this->createResponse('delete', [$id], true);

        return $this;
    }

    /**
     * Delete payment
     *
     * @param integer $id
     *
     * @return self
     */
    public function deletePayment(int $id): self
    {
        $this->createResponse('deletePayment', [$id]);

        return $this;
    }

    /**
     * Download document
     *
     * @param integer $id
     *
     * @return string
     */
    public function downloadInvoice(int $id, string $path = null, string $extension = null): self
    {
        $filename = $id . ($extension ?? $this->extension);
        $this->createResponse('download', [$id], true);

        Storage::put(
            ($path ?? $this->downloadPath) . $filename,
            $this->response[0]
        );

        $this->response = ['path' => ($path ?? $this->downloadPath) . $filename];

        return $this;
    }

    /**
     * Get get$apiName method
     *
     * @param integer $id
     *
     * @return self
     */
    public function get(int $id): self
    {
        $this->createResponse('get', [$id], true);

        return $this;
    }

    /**
     * Get invoice public url
     *
     * @param integer $id
     *
     * @return self
     */
    public function getPublicUrl(int $id): self
    {
        $this->createResponse('getPublicUrl', [$id]);

        return $this;
    }

    /**
     * Call list$apiName method
     *
     * @param array $conditions
     *
     * @return self
     */
    public function list(array $conditions): self
    {
        $this->createResponse(
            'list',
            [
                $conditions['page'] ?? null,
                $conditions['per_page'] ?? 25,
                $conditions['block_id'] ?? null,
                $conditions['partner_id'] ?? null,
                $conditions['payment_method'] ?? null,
                $conditions['payment_status'] ?? null,
                $conditions['start_date'] ?? null,
                $conditions['end_date'] ?? null,
                $conditions['start_number'] ?? null,
                $conditions['end_number'] ?? null,
                $conditions['start_year'] ?? null,
                $conditions['end_year'] ?? null
            ],
            true
        );

        return $this;
    }

    /**
     * Call get$apiName method
     *
     * @param integer $id
     *
     * @return self
     */
    public function update(int $id): self
    {
        $this->createResponse('update', [$this->model, $id], true);

        return $this;
    }

    /**
     * Send invoice in email
     *
     * @param integer $id
     *
     * @return self
     */
    public function sendInvoice(int $id): self
    {
        $this->createResponse('send', [$id], true);

        return $this;
    }

    use ProcessErrorsTrait;

    /**
     * Store called api instance
     *
     * @var Swagger\Client\Api\$Object
     */
    protected $api;

    /**
     * Store called api name
     *
     * @var string
     */
    protected $apiName = null;

    /**
     * Store config instance
     *
     * @var Swagger\Client\Configuration
     */
    protected $config;

    /**
     * Store data
     *
     * @var array
     */
    protected $data = null;

    /**
     * Store called model instance
     *
     * @var object
     */
    protected $model = null;

    /**
     * Store model class name
     *
     * @var string
     */
    protected $modelClassName = null;

    /**
     * Store responses
     *
     * @var array
     */
    protected $response = null;

    /**
     * Store with http info for methods
     *
     * @var boolean
     */
    protected $withHttpInfo = false;

    /**
     * Call the default configuration and set up api key
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey = null)
    {
        $this->httpClient = new Client();
        $this->config = SwaggerConfig::getDefaultConfiguration()
            ->setApiKey('X-API-KEY', $this->checkConfigHelperIsExists() ? config('billingo.api_key') : $apiKey);
    }

    /**
     * Check that config helper funcion is exists, this is laravel specific
     *
     * @return boolean
     */
    protected function checkConfigHelperIsExists(): bool
    {
        return \function_exists('config');
    }

    /**
     * Check if given class is exsits.
     *
     * @param string $className
     *
     * @throws Exception
     * @return void
     */
    protected function classExists(string $className): void
    {
        if (!class_exists($className)) {
            throw new Exception($className . ' class does not exsits!');
        }
    }

    /**
     * Create response
     *
     * @param string $methodName
     * @param array $params
     * @param boolean $customResponse
     *
     * @return void
     */
    protected function createResponse(string $methodName, array $params, bool $methodSuffix = false, bool $customResponse = false)
    {
        try {
            $this->response =
                \call_user_func_array(
                    array(
                        $this->api,
                        $this->setMethodName($methodName, $methodSuffix)
                    ),
                    $params
                );
        } catch (\Throwable $th) {
            echo ($this->error($th->getMessage())->response());
            exit;
        }

        $this->setResponse();
    }

    /**
     * Check if data is present
     *
     * @param array $data
     *
     * @throws Exception
     * @return void
     */
    protected function isData(array $data = null): void
    {
        if (is_null($this->data) and is_null($data)) {
            throw new Exception('Data not set!');
        }
    }

    /**
     * Check if gicen method exists in given class (api instance)
     *
     * @param string $methodName
     *
     * @throws Exception
     * @return void
     */
    protected function methodExists(string $methodName): void
    {
        if (!method_exists($this->api, $methodName)) {
            throw new Exception($methodName . ' method does not exsits!');
        }
    }

    /**
     * Set callable method name
     *
     * @param string $name
     * @param boolean $suffix
     *
     * @return string
     */
    protected function setMethodName(string $name, bool $suffix = false): string
    {
        $methodName = $name . ($suffix ? $this->apiName . ($this->withHttpInfo ? 'withHttpInfo' : '') : '');

        $this->methodExists($methodName);

        return $methodName;
    }

    /**
     * Set response
     *
     * @return void
     */
    protected function setResponse(): void
    {
        if (\is_object($this->response)) {
            $this->response = Arr::collapse($this->toArray((array)$this->response));
        }

        if (\is_array($this->response)) {
            $this->response = $this->toArray($this->response);
        }

        $this->response = (array)$this->response;
    }

    /**
     * Mapping array and if it's conatins object convert it to array because swagger return mixed arrays and objects with protected and private properties
     *
     * @param array $item
     *
     * @return array
     */
    protected function toArray(array $item): array
    {
        return \array_map(function ($item) {
            if (\is_object($item)) {
                return Arr::collapse((array)$item);
            }
            if (\is_array($item)) {
                return $this->toArray($item);
            }
            return $item;
        }, $item);
    }

    /**
     * Make a new api instace
     *
     * @param string $name
     *
     * @throws Exception (classExists)
     * @return self
     */
    public function api(string $name): self
    {
        $className = '\\Swagger\\Client\\Api\\' . $name . 'Api';

        $this->classExists($className);

        $this->apiName = $name;

        $this->api = new $className(
            new \GuzzleHttp\Client(),
            $this->config
        );

        return $this;
    }

    /**
     * Get id from response
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->response['id'];
    }

    /**
     * Get repsonse
     *
     * @return Array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Setup data for model
     *
     * @param array $data
     *
     * @return self
     */
    public function make(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Make a new model instance
     *
     * @param string $name
     * @param array $data
     *
     * @return self
     */
    public function model(string $name, array $data = null): self
    {
        $this->modelClassName = '\\Swagger\\Client\\Model\\' . $name;

        if (!is_null($data)) {
            $this->make($data);
        }

        $this->classExists($this->modelClassName);
        $this->isData();

        $this->model = new $this->modelClassName($this->data);

        return $this;
    }

    /**
     * Set withHttpInfo
     *
     * @return void
     */
    public function withHttpInfo()
    {
        $this->withHttpInfo = true;

        return $this;
    }
}
