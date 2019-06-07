.PHONY: build

deploy: vendor frontend
	sudo sysctl -w vm.max_map_count=262144
	docker-compose up -d
	authbind --deep pm2 start pm2.json

build: vendor
	bin/console doctrine:schema:drop --force
	bin/console doctrine:schema:create

frontend:
	cd ../front/ && npm run build

vendor: composer.json
	composer install
