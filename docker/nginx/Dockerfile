ARG CACHE
FROM ${CACHE}/nginxinc/nginx-unprivileged:1.23.3-alpine
RUN mkdir -p /etc/nginx/templates && touch /tmp/nginx.pid
COPY ./docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template

