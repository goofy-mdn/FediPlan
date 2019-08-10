### How to install FedipPlan

1. Clone the repository

2. Install vendors:

`composer install`

PS: You need to install `composer`, just use:

`apt install composer`

Or you can do that with the following commands:

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

See: [Download Composer](https://getcomposer.org/download/)

### Support My work at [fedilab.app](https://fedilab.app/page/donations/)


### TODO:
    1. Polls
    2. Autocompletion for mentions, tags and emojis
    3. Check and edit scheduled statuses.
    4. Delete media once submitted 