FROM docker.elastic.co/elasticsearch/elasticsearch:7.7.0

COPY plugins /plugins

# Ukrainian
RUN /usr/share/elasticsearch/bin/elasticsearch-plugin install file:///plugins/analysis-ukrainian-7.7.0.zip
# Transliteration
RUN /usr/share/elasticsearch/bin/elasticsearch-plugin install file:///plugins/analysis-icu-7.7.0.zip
