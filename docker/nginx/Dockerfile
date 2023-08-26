FROM nginx

VOLUME ["/var/www/html"]

RUN rm /etc/nginx/conf.d/default.conf
ADD docker/nginx/writer.conf /etc/nginx/conf.d
ADD docker/nginx/reader.conf /etc/nginx/conf.d