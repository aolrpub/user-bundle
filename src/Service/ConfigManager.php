<?php

namespace Aolr\UserBundle\Service;

class ConfigManager
{
    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getAolrPubLink()
    {
        return $this->parameters['aolr_pub_link'];
    }

    public function getAfterLoginRoute()
    {
        return $this->parameters['after_login_route'];
    }

    public function getSupportEmail()
    {
        return $this->parameters['support_email'];
    }

    public function getPublisherName()
    {
        return $this->parameters['publisher_name'];
    }

    public function getLogo()
    {
        return $this->parameters['logo'] ?? '';
    }

    public function getEmails()
    {
        return $this->parameters['emails'] ?? [];
    }
}
