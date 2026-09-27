# BD Courier API Documentation

> Recreated from the supplied `bdcourier.pdf`.\
> This document preserves the endpoint, terminology, request format, and
> example response shown in the source.

## 1. API Overview

**Base URL**

``` text
https://api.bdcourier.com
```

### Authentication

The API uses a Bearer API key.

``` http
Authorization: Bearer YOUR_API_KEY
Content-Type: application/json
```

The supplied documentation displays an API key in the UI. It is not
reproduced here; use your own API key.

------------------------------------------------------------------------

## 2. Available Endpoints

The documentation UI shows the following endpoint sections:

-   Courier Check
-   Courier Check (Legacy)
-   Check Connection
-   My Plan

The supplied page provides detailed request/response documentation for
**Courier Check**. The other sections are visible as navigation tabs,
but their detailed definitions are not present in the supplied page.

------------------------------------------------------------------------

# 3. Courier Check

Retrieve courier tracking information by phone number.

The source states that the endpoint **automatically uses the free or
paid plan based on your subscription**.

### Request

``` http
POST /courier-check
```

### Full URL

``` text
https://api.bdcourier.com/courier-check
```

### Headers

  Header            Required   Value
  ----------------- ---------- -----------------------
  `Content-Type`    Yes        `application/json`
  `Authorization`   Yes        `Bearer YOUR_API_KEY`

### Parameters

  Parameter   Type     Required   Description
  ----------- -------- ---------- -------------------------------------------
  `phone`     string   Yes        Phone number to check, e.g. `017xxxxxxxx`

### Request Body

``` json
{
  "phone": "017xxxxxxxx"
}
```

------------------------------------------------------------------------

## 4. Code Example

### cURL

``` bash
curl --request POST \
  --url https://api.bdcourier.com/courier-check \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer YOUR_API_KEY' \
  --data '{
    "phone": "017xxxxxxxx"
  }'
```

### PHP

``` php
<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.bdcourier.com/courier-check");
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode([
        "phone" => "017xxxxxxxx"
    ])
);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer YOUR_API_KEY"
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

echo $response;
```

------------------------------------------------------------------------

# 5. Response

### Success Response

The source shows a successful response with courier-level statistics, an
overall summary, and fraud reports.

``` json
{
  "status": "success",
  "data": {
    "pathao": {
      "name": "Pathao",
      "logo": "https://api.bdcourier.com/c-logo/pathao-logo.png",
      "total_parcel": 150,
      "success_parcel": 120,
      "cancelled_parcel": 30,
      "success_ratio": 80
    },
    "steadfast": {
      "name": "SteadFast",
      "logo": "https://api.bdcourier.com/c-logo/steadfast-logo.png",
      "total_parcel": 200,
      "success_parcel": 175,
      "cancelled_parcel": 25,
      "success_ratio": 87.5
    },
    "parceldex": {
      "name": "ParcelDex",
      "logo": "https://api.bdcourier.com/c-logo/parceldex-logo.png",
      "total_parcel": 0,
      "success_parcel": 0,
      "cancelled_parcel": 0,
      "success_ratio": 0
    },
    "courierfast": {
      "name": "CourierFast",
      "logo": "https://api.bdcourier.com/c-logo/courierfast-logo.png",
      "total_parcel": 50,
      "success_parcel": 45,
      "cancelled_parcel": 5,
      "success_ratio": 90
    },
    "redx": {
      "name": "Redx",
      "logo": "https://api.bdcourier.com/c-logo/redx-logo.png",
      "total_parcel": 80,
      "success_parcel": 65,
      "cancelled_parcel": 15,
      "success_ratio": 81.25
    },
    "paperfly": {
      "name": "PaperFly",
      "logo": "https://api.bdcourier.com/c-logo/paperfly-logo.png",
      "total_parcel": 100,
      "success_parcel": 90,
      "cancelled_parcel": 10,
      "success_ratio": 90
    },
    "carrybee": {
      "name": "CarryBee",
      "logo": "https://api.bdcourier.com/c-logo/carrybee-logo.webp",
      "total_parcel": 40,
      "success_parcel": 35,
      "cancelled_parcel": 5,
      "success_ratio": 87.5
    },
    "summary": {
      "total_parcel": 620,
      "success_parcel": 530,
      "cancelled_parcel": 90,
      "success_ratio": 85.48
    },
    "reports": [
      {
        "id": "abc123",
        "name": "John Doe",
        "details": "Fraud reported by merchant",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "courierLogo": "https://api.bdcourier.com/c-logo/steadfast-logo.png",
        "courierName": "SteadFast"
      }
    ]
  }
}
```

> **Source fidelity note:** The JSON above reproduces the structure and
> example values shown in the supplied documentation. Where the source's
> displayed example appears internally inconsistent, it has not been
> silently corrected.

------------------------------------------------------------------------

# 6. Response Structure

## Top-Level Object

  Field      Type     Description
  ---------- -------- -------------------------------------------------
  `status`   string   Response status. The example returns `success`.
  `data`     object   Courier results, summary, and reports.

## Courier Result Object

Each courier entry contains:

  Field                Type     Description
  -------------------- -------- -------------------------------------------
  `name`               string   Courier name.
  `logo`               string   Courier logo URL.
  `total_parcel`       number   Total parcel count shown for the courier.
  `success_parcel`     number   Number of successful parcels.
  `cancelled_parcel`   number   Number of cancelled parcels.
  `success_ratio`      number   Success ratio for the courier.

The supplied example contains results for:

-   Pathao
-   SteadFast
-   ParcelDex
-   CourierFast
-   Redx
-   PaperFly
-   CarryBee

------------------------------------------------------------------------

## Summary Object

The `summary` object provides aggregate courier statistics.

  Field                Type     Description
  -------------------- -------- ----------------------------------------
  `total_parcel`       number   Total parcels in the aggregate result.
  `success_parcel`     number   Total successful parcels.
  `cancelled_parcel`   number   Total cancelled parcels.
  `success_ratio`      number   Aggregate success ratio.

Example:

``` json
{
  "total_parcel": 620,
  "success_parcel": 530,
  "cancelled_parcel": 90,
  "success_ratio": 85.48
}
```

------------------------------------------------------------------------

# 7. Fraud Reports

The response may contain a `reports` array.

Each report contains:

  -----------------------------------------------------------------------
  Field                   Type                    Description
  ----------------------- ----------------------- -----------------------
  `id`                    string                  Report identifier.

  `name`                  string                  Name associated with
                                                  the report.

  `details`               string                  Report details.

  `created_at`            string                  Report creation
                                                  timestamp.

  `courierLogo`           string                  Courier logo URL
                                                  associated with the
                                                  report.

  `courierName`           string                  Courier associated with
                                                  the report.
  -----------------------------------------------------------------------

Example:

``` json
{
  "id": "abc123",
  "name": "John Doe",
  "details": "Fraud reported by merchant",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "courierLogo": "https://api.bdcourier.com/c-logo/steadfast-logo.png",
  "courierName": "SteadFast"
}
```

------------------------------------------------------------------------

# 8. Integration Flow

A typical integration based on the supplied documentation is:

1.  Obtain a BD Courier API key.
2.  Store the API key securely on the server.
3.  Send a `POST` request to `/courier-check`.
4.  Include the phone number in the JSON request body.
5.  Send the Bearer token in the `Authorization` header.
6.  Read `status` from the response.
7.  Read courier-specific statistics from `data`.
8.  Read aggregate statistics from `data.summary`.
9.  Read any available fraud information from `data.reports`.

### Example

``` text
Customer Phone
      |
      v
POST /courier-check
      |
      |  Authorization: Bearer YOUR_API_KEY
      |  { "phone": "017xxxxxxxx" }
      v
BD Courier API
      |
      v
Courier Statistics
      |
      +--> Pathao
      +--> SteadFast
      +--> ParcelDex
      +--> CourierFast
      +--> Redx
      +--> PaperFly
      +--> CarryBee
      |
      +--> Summary
      |
      +--> Fraud Reports
```

------------------------------------------------------------------------

# 9. Security

Do not expose the API key in frontend JavaScript, public repositories,
mobile application source code, or client-side requests.

Recommended server-side configuration:

``` env
BDCOURIER_API_URL=https://api.bdcourier.com
BDCOURIER_API_KEY=your_api_key_here
```

Then send:

``` http
Authorization: Bearer ${BDCOURIER_API_KEY}
```

------------------------------------------------------------------------

# 10. Source Coverage

This Markdown document is based strictly on the supplied
`bdcourier.pdf`.

The supplied page visibly documents:

-   Base API URL
-   API key authentication
-   Courier Check endpoint
-   POST request
-   `phone` parameter
-   PHP request example
-   Successful response structure
-   Courier statistics
-   Aggregate summary
-   Fraud reports
-   Endpoint navigation for:
    -   Courier Check
    -   Courier Check (Legacy)
    -   Check Connection
    -   My Plan

Detailed request/response definitions for **Courier Check (Legacy)**,
**Check Connection**, and **My Plan** are not visible in the supplied
page, so they have not been invented or supplemented from external
sources.
