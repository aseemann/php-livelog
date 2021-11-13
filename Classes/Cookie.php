<?php
declare(strict_types=1);

namespace ASeemann\PhpLiveLog;


use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;

/**
 * Class Cookie
 *
 * @package ASeemann\PhpLiveLog
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 *
 */
class Cookie
{
    public const COOKIE_NAME = "PHPLIVELOG";

    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * Cookie constructor.
     */
    public function __construct()
    {
        $this->setUuid(Uuid::uuid4())
            ->injectValidator(new Validator());
    }

    /**
     * Set the uuid object
     *
     * @param UuidInterface $uuid
     *
     * @return $this
     */
    public function setUuid(UuidInterface $uuid): Cookie
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Inject the validator
     *
     * @param ValidatorInterface $validator
     *
     * @return $this
     */
    public function injectValidator(ValidatorInterface $validator): Cookie
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the uuid cookie for the live logger
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function setCookie(): void
    {
        if (false === filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_DOMAIN)) {
            return;
        }
        $hostname = strip_tags($_SERVER['HTTP_HOST']);
        $uuid = $this->uuid->toString();
        setcookie(self::COOKIE_NAME, $uuid, 0, '/', $hostname);
        $_COOKIE[self::COOKIE_NAME] = $uuid;
    }

    /**
     * Returns the content of the cookie.
     *
     * (i) This method will also set a new cookie if its empty or the content is not a valid uuid.
     *
     * @return string
     */
    public function getCookie(): string
    {
        if (false === $this->hasCookie() || false === $this->isCookieValid()) {
            $this->setCookie();
        }

        return $_COOKIE[self::COOKIE_NAME];
    }

    /**
     * Returns true if the cookie is set
     *
     * @return bool
     */
    public function hasCookie(): bool
    {
        return false === empty($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * Returns true if the cookie content is a valid uuid
     *
     * @return bool
     */
    public function isCookieValid(): bool
    {
        return $this->validator->validate($_COOKIE[self::COOKIE_NAME]);
    }
}
