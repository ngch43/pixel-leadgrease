<?php


namespace App\Api;


use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * ApiResponse constructor.
     *
     * @param string $message
     * @param mixed  $data
     * @param array  $errors
     * @param int    $status
     * @param array  $headers
     * @param bool   $json
     */
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($this->format($data), $status, $headers, $json);
    }

    /**
     * Format the API response.
     *
     * @param string $message
     * @param mixed  $data
     * @param array  $errors
     *
     * @return array
     */
    private function format($data = null)
    {
        if ($data === null) {
            $response['data'] = new \ArrayObject();
        }else{
            $response['data'] = $data;
        }
        return $response;
    }
}