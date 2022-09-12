
quick-test:
	./bin/ci

integration-test:
	docker-compose up -d
	sleep 20
	./vendor/bin/phpunit -c phpunit.xml.integration.dist -v --group integration
	docker-compose down

clean:
	docker-compose down
