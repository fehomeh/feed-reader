# base image
FROM node:12.2.0 as angular_php

# set working directory
WORKDIR /app

# add `/app/node_modules/.bin` to $PATH
ENV PATH /app/node_modules/.bin:$PATH

# install and cache app dependencies
RUN npm install -g @angular/cli@7.3.9

# add app
COPY ./front/feed /app
# COPY package.json ./
RUN npm install

# start app
CMD ng serve --host 0.0.0.0
