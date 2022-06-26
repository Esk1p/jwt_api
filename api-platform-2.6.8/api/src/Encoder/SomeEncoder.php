<?php

namespace App\Encoder;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use JWT\Authentication\JWT;

/**
 * NixillaJWTEncoder
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class SomeEncoder implements JWTEncoderInterface
{
    private $key;

    public function __construct(string $key = 'apple')
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $data)
    {
        try {
            return JWT::encode($data, $this->key);
        }
        catch (\Exception $e) {
            throw new JWTEncodeFailureException(JWTEncodeFailureException::INVALID_CONFIG, 'An error occurred while trying to encode the JWT token.', $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token)
    {
        try {
            return (array) JWT::decode($token, $this->key);
        } catch (\Exception $e) {
            throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'Invalid JWT Token', $e);
        }
    }
}
