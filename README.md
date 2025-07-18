## INTRODUCTION

The Fuel Calculator module is a custom Drupal module designed to assist users in
estimating fuel costs for their journeys. By leveraging a REST API endpoint, it
calculates the total fuel cost and consumption based on user-provided inputs
such as distance, fuel efficiency, and fuel price.
This module is ideal for websites or applications that require fuel cost
estimation functionality.

## REQUIREMENTS

- Drupal 10/11
- Rest

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION

- /admin/config/services/fuel-calculator - Settings for default form values.

## FORM URL

- /fuel-calculator - Form dedicated page.

## FORM BLOCK

`Fuel Calculator Form` - name of the Fuel Calculator Block

## REST API INSTRUCTION

The Fuel Calculator module provides a REST API endpoint for fuel calculation.

### Endpoint

`POST /api/fuel-calculate`

### Request Format

The API expects a JSON payload with the following structure:
```json
{
  "distance_travelled": <number>, // Distance to be traveled (in kilometers)
  "fuel_consumption": <number>, // Fuel efficiency (liters per 100km)
  "price_per_liter": <number> // Price of fuel per liter
}
```

### Response Format

The API returns a JSON response with the calculated fuel cost:
```json
{
  "fuel_cost": <number>, // Total cost of fuel for the given distance
  "fuel_spent": <number> // Total amount of fuel burnt for the given distance
}
```

### Example Request Payload

```json
{
    "distance_travelled": "100",
    "fuel_consumption": "5",
    "price_per_liter": "10"
}
```

### Example Response

```json
{
    "fuel_cost": 50,
    "fuel_spent": 5
}
```

### Notes

- Ensure the REST module is enabled and configured properly.
- Authentication may be required depending on your site's configuration.
- Validate input data to avoid calculation errors.
- Ensure correct permissions are enabled for this resource.



## MAINTAINERS

Current maintainers for this module:

- Rafal Tokarski - https://www.drupal.org/u/rafaltokarski
