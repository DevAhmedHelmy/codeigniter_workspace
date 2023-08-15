<?php

namespace App\Filters;

use Exception;
use App\Models\Token;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    use ResponseTrait;
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {

        $key = getenv('JWT_SECRET');
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }


        // check if token is null or empty
        if (is_null($token) || empty($token)) {
            return $this->invalidToken();
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // Check if the token exists in the database
            $existsToken = (new Token())->where('email', $decoded->email)->where('token', $token)->first();
            if (is_null($existsToken)) {
                return $this->invalidToken();
            }

            // Token is valid, continue with the request
            return $request;
        } catch (Exception $ex) {
            return $this->invalidToken();
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    private function invalidToken()
    {
        $response = service('response');
        $response->setBody('Access denied');
        $response->setStatusCode(401);
        return $response;
    }
}
