# FixItNow — Week 5: Embed map/location on booking page

Uses **Leaflet** (a free JS mapping library) with **OpenStreetMap** tiles
and **Nominatim** (OpenStreetMap's free geocoding) to turn a typed address
into a pin on a map — no billing setup, no API key.

**Important — read before using Nominatim:** it's a shared public service
with a real usage policy: https://operations.osmfoundation.org/policies/nominatim/
Max 1 request/second, you must identify your app with a real User-Agent
(already done in `GeocodingService.php`), and bulk geocoding isn't allowed.
This is fine for FixItNow because geocoding only fires once per booking
submission — a single human action, not a loop — which is exactly the
kind of one-off lookup the policy permits.

## 1. Install the Leaflet package

In your terminal:

```bash
npm install leaflet
```

## 2. Copy these files in

```
database/migrations/2026_10_18_000003_add_coordinates_to_bookings_table.php  (new)
app/Services/GeocodingService.php                           (new)
app/Models/Booking.php                                      (overwrite)
app/Http/Controllers/BookingController.php                  (overwrite)
resources/js/map.js                                          (new)
resources/views/bookings/show.blade.php                     (overwrite)
```

## 3. Load the map script

Open `resources/js/app.js` and add this one line near the top (after any
existing imports):

```js
import './map.js';
```

This bundles the map code into your existing app.js build — no changes to
`vite.config.js` needed.

## 4. Run the migration and rebuild assets

```bash
php artisan migrate
npm run build
```

(Or `npm run dev` if you're actively working and want it to rebuild on
save.)

## 5. Test it

1. Book a service as a customer using a **real, specific address**
   (e.g. "SM City Marikina, Sumulong Highway, Marikina" rather than
   something vague like "my house"). Submit.
2. On the confirmation page, you should see an embedded map with a pin at
   that location. Click the pin — it should show a popup with the address.
3. Try a booking with a deliberately bad/unresolvable address (e.g.
   random gibberish). Confirm the booking still gets created — the map
   section should instead show "Map location not available for this
   address" instead of erroring out.
4. Log in as the assigned technician (or admin) and open the same
   booking — confirm the map renders for them too, not just the customer
   who submitted it.

## Notes for your defense

- If asked "why not Google Maps," the honest answer is: Google's
  Geocoding and Maps APIs require a billing account even within the free
  tier, which isn't practical for a 10-person student group project with
  no shared payment method. OpenStreetMap + Nominatim needs no account or
  card at all.
- If asked about Nominatim's rate limit, the answer is that it only
  matters for bulk/automated lookups — FixItNow only geocodes once per
  booking a real person submits, so it's never going to approach the
  1-request-per-second ceiling.
- The coordinates are stored on the booking at creation time (not
  re-geocoded every time the page loads), which is both faster and kinder
  to the free service.
