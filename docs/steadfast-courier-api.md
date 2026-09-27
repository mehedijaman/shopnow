**SteadFast Courier API**

**Recreated Developer Documentation**

_Based on the supplied SteadFast API Guide PDF_

# 1\. Overview

This document reorganizes the supplied API guide into a conventional developer reference. Endpoint names, request fields, response structures, limits, statuses, and operational notes are preserved from the source document. Example IDs and timestamps are illustrative values taken from the guide.

Base URL:

```
https://portal.packzy.com/api/v1
```

# 2\. Authentication

Every request except GET /ping requires the following headers:

| **Header**   | **Required** | **Description**  |
| ------------ | ------------ | ---------------- |
| Api-Key      | Yes          | Your API key.    |
| Secret-Key   | Yes          | Your secret key. |
| Content-Type | POST         | application/json |

There is no login step and no token-refresh flow. Both keys are sent on every authenticated request.

## Authentication and rate-limit behavior

- A missing or incorrect credential returns HTTP 401.
- After 10 authentication failures from one address/key combination within five minutes, requests are refused with HTTP 429 for 60 minutes, including requests made with the correct keys during the lockout.
- Rate limits are 1,000 requests per minute overall and 6,000 for booking.
- Keys belong to a profile/business. A business key can book and read only that business's parcels and payouts.
- Multiple keys can be held per profile, allowing separate integrations and safer key rotation.

# 3\. Getting Started

## Service check

**GET /ping** — Connectivity check; no API key required.

```
{
  "status": 200,
  "message": "pong",
  "service": "steadfast-api",
  "version": "v1",
  "time": "2026-09-20T13:45:06+06:00"
}
```

Use /ping to verify that the API is reachable. A successful ping does not verify your credentials or the health of booking/database/queue operations. GET /get_balance can be used to verify authenticated access. The returned time uses ISO 8601 with the Asia/Dhaka offset.

# 4\. Important Data Handling Rules

Before storing an address, name, or note, the service replaces certain characters with spaces: {, }, ;, &lt;, and &gt;. Therefore, text sent to the API may not be returned exactly as submitted.

| **Field**         | **Maximum length** |
| ----------------- | ------------------ |
| invoice           | 100 characters     |
| recipient_name    | 100 characters     |
| recipient_phone   | 40 characters      |
| recipient_address | 490 characters     |
| note              | 480 characters     |

# 5\. Booking Parcels

**POST /create_order** — Book one parcel.

| **Field**         | **Type** | **Required** | **Description**                                                                                                 |
| ----------------- | -------- | ------------ | --------------------------------------------------------------------------------------------------------------- |
| invoice           | string   | Yes          | Your own order number. Letters, digits, hyphens and underscores only; must be unique. Stored to 100 characters. |
| recipient_name    | string   | Yes          | Recipient name. Stored to 100 characters.                                                                       |
| recipient_phone   | string   | Yes          | Eleven digits beginning 01.                                                                                     |
| recipient_address | string   | Yes          | Recipient address. Stored to 490 characters.                                                                    |
| cod_amount        | number   | Yes          | Amount to collect at delivery in BDT. 0 for prepaid. Maximum 1,000,000.                                         |
| note              | string   | No           | Delivery instructions. Up to 480 characters.                                                                    |
| alternative_phone | string   | No           | Second number to try. Eleven digits beginning 01.                                                               |
| recipient_email   | string   | No           | Must be a deliverable address; domain is checked.                                                               |
| item_description  | string   | No           | Parcel contents. Up to 255 characters.                                                                          |
| total_lot         | number   | No           | Number of items. Defaults to 1.                                                                                 |
| delivery_type     | number   | No           | 0 = home delivery (default), 1 = point delivery.                                                                |

```
{
  "invoice": "ORD-10231",
  "recipient_name": "Jahid Hasan",
  "recipient_phone": "01712345678",
  "recipient_address": "House 17/1, Road 3/A, Dhanmondi, Dhaka-1260",
  "cod_amount": 1060,
  "note": "Call before 3 PM"
}
{
  "status": 200,
  "message": "Consignment has been created successfully.",
  "consignment": {
    "consignment_id": 1424107,
    "invoice": "ORD-10231",
    "tracking_code": "15BAE8BA",
    "tracking_link": "https://steadfast.com.bd/tl/a1b2c3d4",
    "recipient_name": "Jahid Hasan",
    "recipient_phone": "01712345678",
    "recipient_address": "House 17/1, Road 3/A, Dhanmondi, Dhaka-1260",
    "recipient_email": null,
    "alternative_phone": null,
    "item_description": null,
    "total_lot": 1,
    "cod_amount": 1060,
    "status": "in review",
    "note": "Call before 3 PM"
  }
}
```

Keep consignment_id and tracking_code for subsequent status/tracking requests. Newly created parcels start in in_review and are approved shortly after.

## Bulk booking

**POST /create_order/bulk-order** — Book up to 500 parcels in one request.

| **Field** | **Type** | **Required** | **Description**                                                                               |
| --------- | -------- | ------------ | --------------------------------------------------------------------------------------------- |
| data      | array    | Yes          | Array of orders, each shaped like a single create_order body. Maximum 500 orders per request. |

```
{
  "data": [
    {
      "invoice": "ORD-10231",
      "recipient_name": "Jahid Hasan",
      "recipient_phone": "01712345678",
      "recipient_address": "Dhanmondi, Dhaka",
      "cod_amount": 1060,
      "note": "Call first"
    },
    {
      "invoice": "ORD-10232",
      "recipient_name": "Nusrat Jahan",
      "recipient_phone": "01812345678",
      "recipient_address": "Uttara, Dhaka",
      "cod_amount": 450
    }
  ]
}
```

The HTTP response is 200 even when individual orders fail. Read every data entry. Results remain in request order. A successful entry has status = success and a consignment_id; a failed entry has status = null and an error array.

```
{
  "status": 200,
  "message": "We have a response for you.",
  "data": [
    {
      "invoice": "ORD-10231",
      "consignment_id": 1424107,
      "tracking_code": "15BAE8BA",
      "status": "success",
      "tracking_link": "https://steadfast.com.bd/tl/a1b2c3d4"
    },
    {
      "invoice": "ORD-10232",
      "consignment_id": null,
      "tracking_code": null,
      "status": null,
      "error": "["THIS_INVOICE_ALREADY_EXISTS"]"
    }
  ]
}
```

## Bulk booking — extended

**POST /create_order/bulk-order/extended** — Bulk booking with per-field validation messages.

| **Field** | **Type** | **Required** | **Description**                                    |
| --------- | -------- | ------------ | -------------------------------------------------- |
| data      | array    | Yes          | Same order structure as above. Maximum 500 orders. |

```
{
  "status": 200,
  "message": "Processed bulk order entries.",
  "data": [
    {
      "invoice": "ORD-10232",
      "status": "error",
      "error": ["The recipient phone format is invalid."],
      "consignment_id": null,
      "tracking_code": null
    }
  ]
}
```

The extended endpoint is intended for new integrations because validation failures are returned as human-readable messages. The guide also notes support for recipient_email, alternative_phone, item_description and total_lot per order.

## Bulk validation error codes

| **Code**                                              | **Meaning**                                                                    |
| ----------------------------------------------------- | ------------------------------------------------------------------------------ |
| INVOICE_IS_REQUIRED                                   | No invoice on the order.                                                       |
| INVOICE_MUST_BE_ALPHA_NUM_DASH_UNDERSCORE             | Invoice contains a character other than a letter, digit, hyphen or underscore. |
| INVOICE_LENGTH_SHOULD_BE_LESS_THAN_100_CHARS          | Invoice is too long.                                                           |
| THIS_INVOICE_ALREADY_EXISTS                           | A parcel has already been booked with this invoice.                            |
| RECEIVER_NAME_IS_REQUIRED                             | No recipient_name.                                                             |
| RECEIVER_NAME_LENGTH_SHOULD_BE_LESS_THAN_100_CHARS    | Recipient name is too long.                                                    |
| RECEIVER_PHONE_IS_REQUIRED                            | No recipient_phone.                                                            |
| RECEIVER_PHONE_LENGTH_SHOULD_BE_LESS_THAN_40_CHARS    | Phone number is too long.                                                      |
| RECEIVER_ADDRESS_IS_REQUIRED                          | No recipient_address.                                                          |
| RECEIVER_ADDRESS_LENGTH_SHOULD_BE_LESS_THAN_500_CHARS | Address is too long.                                                           |
| RECEIVER_NOTE_LENGTH_SHOULD_BE_LESS_THAN_500_CHARS    | Note is too long.                                                              |
| COD_AMOUNT_ERROR                                      | No cod_amount or cod_amount is not a number.                                   |

# 6\. Checking Parcel Status

Status answers are cached for 60 seconds. Polling faster than this does not provide newer information; webhooks are recommended.

**GET /status_by_cid/{consignment_id}** — Status by SteadFast consignment ID.

```
{"status": 200, "delivery_status": "delivered"}
```

**GET /status_with_return_status_by_cid/{consignment_id}** — Status including progress of a parcel returning to the sender.

```
{"status": 200, "delivery_status": "cancelled_return_rider_assigned"}
```

**GET /status_by_invoice/{invoice}** — Status by your own invoice/order number.

```
{"status": 200, "delivery_status": "pending"}
```

**GET /status_by_trackingcode/{tracking_code}** — Status by tracking code.

```
{"status": 200, "delivery_status": "in review"}
```

If the same invoice was booked more than once, the invoice endpoint answers for the most recent one.

**GET /trackings_by_invoice/{invoice}** — Return the parcel's tracking history, not only its current status.

```
{
  "status": 200,
  "tracking": [
    {
      "consignment_id": 1424107,
      "tracking_type": 1,
      "text": "Consignment created by Sender(API).",
      "created_at": "2026-09-20T07:05:31.000000Z"
    },
    {
      "consignment_id": 1424107,
      "tracking_type": 2,
      "text": "Parcel received at Dhanmondi hub.",
      "created_at": "2026-09-26T11:22:04.000000Z"
    }
  ]
}
```

# 7\. Pickups

**POST /create_pickup_request** — Request a rider to collect booked parcels from one of your addresses.

| **Field**         | **Type** | **Required** | **Description**                                |
| ----------------- | -------- | ------------ | ---------------------------------------------- |
| address_id        | number   | Yes          | Saved pickup address ID.                       |
| police_station_id | number   | Yes          | Thana ID from /police_stations.                |
| address           | string   | Yes          | Pickup address, up to 255 characters.          |
| contact_number    | string   | Yes          | Eleven digits beginning 013–019.               |
| note              | string   | No           | Additional instructions, up to 500 characters. |
| estim_qty         | number   | No           | Approximate number of parcels waiting.         |

```
{
  "address_id": 42,
  "police_station_id": 17,
  "address": "House 17/1, Road 3/A, Dhanmondi, Dhaka",
  "contact_number": "01712345678",
  "estim_qty": 25
}
{
  "message": "Pickup request created successfully.",
  "data": {
    "id": 9081,
    "user_id": 12345,
    "user_address_id": 42,
    "police_station_id": 17,
    "pickup_location": "House 17/1, Road 3/A, Dhanmondi, Dhaka",
    "contact_number": "01712345678",
    "note": null,
    "estim_qty": 25,
    "req_status": "...",
    "created_at": "..."
  }
}
```

Successful pickup creation returns HTTP 201. If the same address already has a pending request, the API returns HTTP 409 with PICKUP_REQUEST_EXISTS.

# 8\. Returns

**POST /create_return_request** — Request a parcel to be brought back before delivery.

| **Field**      | **Type** | **Required** | **Description**                          |
| -------------- | -------- | ------------ | ---------------------------------------- |
| consignment_id | number   | No\*         | Parcel identifier.                       |
| invoice        | string   | No\*         | Your order number.                       |
| tracking_code  | string   | No\*         | Tracking code.                           |
| reason         | string   | No           | Reason for return, up to 500 characters. |

\* Identify the parcel using whichever of consignment_id, invoice, or tracking_code you have.

```
{
  "invoice": "ORD-10231",
  "reason": "Customer changed their mind"
}
{
  "id": 1,
  "user_id": 12345,
  "consignment_id": 1424107,
  "reason": "Customer changed their mind",
  "status": "pending",
  "created_at": "...",
  "updated_at": "..."
}
```

Returns are accepted with HTTP 201. A request is refused with 422 if the parcel is already delivered or already coming back. A second request is also refused with 422 while the first remains open.

Return request statuses: pending, approved, processing, completed, cancelled.

**GET /get_return_requests** — List return requests, newest first, ten per page.

Pagination: add ?page=2 for the next ten.

**GET /get_return_request/{id}** — Get one return request, including the parcel it concerns.

# 9\. Balance and Payments

**GET /get_balance** — Get the current balance owed to you.

```
{
  "status": 200,
  "current_balance": 12450
}
```

The guide describes this as delivered COD less delivery charges and the 1% collection charge.

**GET /payments** — List payouts made to you, ten per page.

```
{
  "status": 1,
  "alertclass": "success",
  "message": "Fetched successfully.",
  "payments": [
    {
      "payment_id": "SFC-88213",
      "amount": 12450,
      "method": "bank",
      "due_bills": 0,
      "paid_bills": 3200,
      "charges": 124,
      "total": 12450,
      "status_label": "Paid",
      "created_at": "2026-09-18 11:04:22",
      "ready_at": "2026-09-18 12:00:00",
      "paid_at": "2026-09-19 10:31:07"
    }
  ]
}
```

**GET /payments/{payment_id}** — Get one payout and every parcel it settled.

The payment ID may be supplied as digits; for example, SFC-88213 is treated as 88213 and non-digits are ignored. The endpoint is intended for payout reconciliation.

# 10\. Lookup Endpoints

**GET /police_stations** — List every thana served, with its district.

The guide notes that this data changes rarely and should be fetched occasionally rather than for every order.

**GET /fraud_check/score/{phone}** — Return the customer's delivery-history indicators.

```
{
  "status": 200,
  "phone": "01712345678",
  "delivery_ratio": 92,
  "cancellation_ratio": 7,
  "volume_band": "high",
  "total_reports": 0,
  "fraud_categories": {},
  "score": null,
  "level": null,
  "reasons": [],
  "scoring_disabled": true,
  "doubtful_reports": false
}
```

The source states that scoring is disabled: score and level return null, reasons is empty, and scoring_disabled is true. Use delivery_ratio, cancellation_ratio, volume_band, total_reports and fraud_categories instead.

delivery_ratio and cancellation_ratio are whole percentages of finished parcels; partially delivered counts as delivered. Both are null when no parcels have finished. volume_band is one of none, low (1–5), medium (6–20), high (21–200), or very_high (200+). fraud_categories contains report categories and counts, where available.

The endpoint is rate-limited against the merchant's parcel volume. The source recommends roughly four checks per parcel booked on the busiest day in the last week, plus ten. The guide says the older fraud-count endpoint is being deprecated and will stop returning counts on 27 September 2026.

# 11\. Delivery Status Reference

| **Status**                         | **Meaning**                                                           |
| ---------------------------------- | --------------------------------------------------------------------- |
| pending                            | Booked and with SteadFast; not yet attempted.                         |
| in review                          | Just created and awaiting approval.                                   |
| hold                               | Held, usually for an address or payment question.                     |
| delivered_approval_pending         | Rider marked delivered; accounts team has not confirmed it.           |
| partial_delivered_approval_pending | Rider marked partially delivered; awaiting confirmation.              |
| cancelled_approval_pending         | Rider marked cancelled; awaiting confirmation.                        |
| unknown_approval_pending           | Awaiting confirmation; outcome is not one of the above.               |
| delivered                          | Delivered and confirmed; COD is owed to you.                          |
| partial_delivered                  | Part of the order was delivered and confirmed.                        |
| cancelled                          | Not delivered and confirmed; parcel comes back to you.                |
| exceptional                        | Lost, damaged, or otherwise outside the normal path; contact support. |
| unknown                            | State not recognized by the API; treat as 'ask us'.                   |

Any status ending in \_approval_pending is not final. The guide recommends waiting for delivered, partial_delivered, or cancelled before settling books.

# 12\. Return Status Reference

| **Status**                              | **Meaning**                                            |
| --------------------------------------- | ------------------------------------------------------ |
| partial_delivered_return_processing     | Part delivered; the rest is being prepared for return. |
| partial_delivered_return_rider_assigned | Part delivered; rider is bringing the rest back.       |
| partial_delivered_return_received       | Part delivered; the remaining part has been returned.  |
| cancelled_return_processing             | Cancelled; parcel is being prepared for return.        |
| cancelled_return_rider_assigned         | Cancelled; rider is bringing it back.                  |
| cancelled_return_received               | Cancelled; parcel has been returned.                   |

# 13\. Polling vs Webhooks

Status responses are cached for 60 seconds. Polling more frequently consumes requests without producing newer status information. The guide recommends providing a webhook URL so SteadFast can notify the integration when a parcel moves, a return list is accepted, or a payout is made.

# 14\. HTTP Response Codes

| **Code** | **Meaning**                                                                          | **Recommended action**                                                                                    |
| -------- | ------------------------------------------------------------------------------------ | --------------------------------------------------------------------------------------------------------- |
| 200      | Done or answered. Some endpoints can report per-item failures inside a 200 response. | Always inspect the response body, especially bulk results.                                                |
| 201      | Created.                                                                             | Process the newly created resource.                                                                       |
| 400      | Request was not understood; commonly malformed or missing data in a bulk request.    | Validate the request structure and fields.                                                                |
| 401      | Unauthenticated, or credentials are wrong/revoked.                                   | Fix API credentials; do not retry blindly.                                                                |
| 403      | Authenticated, but not allowed right now.                                            | Check account/permission/ownership state.                                                                 |
| 409      | Conflict, such as an already-pending pickup request.                                 | Check whether the resource already exists/pending before retrying.                                        |
| 422      | A field or operation was rejected.                                                   | Read the error details and correct the request/state.                                                     |
| 429      | Too many requests or too many authentication failures.                               | Back off; repeated bad credentials can lock the client.                                                   |
| 500      | Server-side fault.                                                                   | Retry with the same invoice where appropriate; duplicate invoices are refused, preventing double booking. |

# 15\. Integration Notes

- Use GET /ping first when diagnosing connectivity.
- For authenticated connectivity, GET /get_balance can expose credential problems.
- Persist consignment_id and tracking_code immediately after a successful booking.
- Treat bulk booking as partially successful: process each result independently.
- Do not settle financial records on \*\_approval_pending statuses.
- Use /status_with_return_status_by_cid/{consignment_id} when inventory reconciliation requires confirmation that a return is actually back.
- Prefer webhooks over status polling where possible.
- Respect the 60-second status cache and the documented rate limits.
- Keep API keys scoped to the relevant business/profile and integration.

_Recreated from the supplied SteadFast Courier API Guide PDF_