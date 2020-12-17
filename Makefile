all:
	docker run --rm -w /app -v "$(shell pwd):/app" -i ngyuki/php-dev:7.0 \
		php clover-to-cobertura.php < tests/clover.xml > tests/cobertura.xml
