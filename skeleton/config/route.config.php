<?php exit(0); ?>
# In this file the order of the route entries matters!
# The entries closer to the top have higher priority,
# than the entries underneath. So a good tip is to place
# domain specific entries (local routes) before any global
# interceptor (global routes)

# Local routes / domain specific
GET www.myexampledomain.com/					\myexampledomain\MyExampleDomain.index

# Global routes / valid for all hosted domains
GET /  										Application.index # this is catch all site in case that no local routing is defined
GET /license                                Application.license
#GET /{action} 								Application.{action}
#GET /{controller}/{action} 				{controller}.{action}

# Static routes
# The static routes use always get method! Otherwise the route will be invalid.
GET /robots.txt 							staticFile:/public/robots/robots.txt
GET /js 									staticDir:/public/js
GET /css 									staticDir:/public/css
GET /images 								staticDir:/public/images