<?php exit(0); ?>

# defines the current environment; values: "dev"|"prod"
application.environment=dev

# Currently not supported!
# defines where the application is situated
# i.e.: if the application is used as a plugin 
# then the base url can be /plugin/abc
# so every request sent to /plugin/abc
# will be redirected to the plugin
# 
# by default this is empty
base.url=

# simulates that the requests are coming from that domain
# defines the default server domain for the application
# base.domain is used only in dev application environment
# in order to made possible working on domains which are
# different then localhost. In prod mode only the configuration
# in the routes.config are considered to be valid
# example config in the routes:
# GET mydomain.com/ MyDomainController.index
# default value is empty or localhost
base.domain=
#base.domain=www.myexampledomain.com

# The path to the application mvc relative to the application root
application.mvc=app

# log file relative to the application root
application.logfile=logs/application.log

# Temp path relative from the application root
application.temp=tmp

# Data path relative from the application root
application.data=data

# Views path relative from the application app folder
application.views=views

# Models path relative from the application app folder
application.models=models

# Controllers path relative from the application app folder
application.controllers=controllers
