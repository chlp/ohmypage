up:
	docker-compose up -d && sleep 5
	docker-compose run --rm composer install

down:
	docker-compose stop

clean:
	$(MAKE) down
	docker-compose rm -fv
