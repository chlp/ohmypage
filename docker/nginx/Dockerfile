FROM nginx

VOLUME ["/var/www"]

RUN rm /etc/nginx/conf.d/default.conf
ADD docker/nginx/writer.conf /etc/nginx/conf.d
ADD docker/nginx/reader.conf /etc/nginx/conf.d
ADD docker/nginx/images.conf /etc/nginx/conf.d
ADD docker/nginx/videos.conf /etc/nginx/conf.d
ADD docker/nginx/files.conf /etc/nginx/conf.d