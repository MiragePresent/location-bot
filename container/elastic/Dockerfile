FROM docker.elastic.co/elasticsearch/elasticsearch:7.7.0

# Ukrainian
RUN /usr/share/elasticsearch/bin/elasticsearch-plugin install analysis-ukrainian
# Transliteration
RUN /usr/share/elasticsearch/bin/elasticsearch-plugin install analysis-icu
