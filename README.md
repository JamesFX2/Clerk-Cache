# Clerk Cache
An open source endpoint proxy for Clerk to reduce the load on Clerk's servers

# Features

Locally caches searches and eventually product recommendations so that calls to Clerk's servers are reduced. An endpoint intercepts searches and product SKUs, calls Clerk's API and stores the results for 48 hours. This reduces the load on Clerk's servers by ensuring that duplicate searches or views of the same product only generate 1 call to Clerk. 

# Installation

Copy the files to a spare location on your website or appropriate hosting. 


# To dos

- Switch from JSON to MongoDB implementation
- Finish recommendation caching
