## user-bundle


Configuration example:
```
aolr_user:
    support_email: "%env(SUPPORT_EMAIL)%"
    after_login_route: "homepage"
    aolr_pub_link: "www.aolr.pub"
    publisher_name: "Aolr PaaS"
    privacy_url: "https://aolr.pub/en/page/privacy"
    logo: "/images/logo.png"
    manager_name: "paas"
    emails:
        from: "%env(SUPPORT_EMAIL)%"
        validation:
            body: "email/user/registration_validation.html.twig"
```
