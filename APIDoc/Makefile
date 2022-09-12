all: swagger-doc compile deploy clean

swagger-doc:
	swagger-cli validate HamTajrobe.yml
	swagger-cli bundle HamTajrobe.yml --outfile _build/OpenApi.yml --type yaml

compile:
	emrichen nelmio_api_doc_template.yml > _build/nelmio_api_doc.yml

deploy:
	cp _build/nelmio_api_doc.yml ../config/packages/nelmio_api_doc.yaml

clean:
	rm -rf _build