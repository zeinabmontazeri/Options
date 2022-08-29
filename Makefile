all: composer db-init jwt-init

composer:
	composer install

db-init:
ifeq ($(shell php db_check.php), notexists)
	symfony console doctrine:database:create
endif
	symfony console doctrine:schema:drop --force
	symfony console doctrine:schema:update --force
	symfony console doctrine:fixtures:load --no-interaction

jwt-init:
	symfony console lexik:jwt:generate-keypair --overwrite --no-interaction