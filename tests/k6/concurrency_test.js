import http from 'k6/http';
import { check } from 'k6';
import { Counter, Trend } from 'k6/metrics';

const successfulBookings = new Counter('successful_bookings');
const conflictsDetected = new Counter('conflicts_detected');
const databaseErrors = new Counter('database_errors');
const bookingLatency = new Trend('booking_latency');

export const options = {
    scenarios: {
        burst_race_condition: {
            executor: 'per-vu-iterations',
            vus: 100,
            iterations: 1,
            maxDuration: '10s',
        },
    },
    thresholds: {
        'successful_bookings': ['count==1'],
        'database_errors': ['count==0'],
    },
};

const BASE_URL = __ENV.BASE_URL || 'http://localhost/api';
const STRATEGY = __ENV.STRATEGY || 'pessimistic';

export function setup() {
    const payload = JSON.stringify({
        email: 'tryfonopoulos@uop.gr',
        password: 'password123',
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', // <-- THIS PREVENTS THE HTML WALL OF TEXT
        },
    };

    const loginRes = http.post(`${BASE_URL}/auth/login`, payload, params);

    if (loginRes.status !== 200) {
        throw new Error(`Login failed: [HTTP ${loginRes.status}] ${loginRes.body}`);
    }

    const token = loginRes.json('token') || loginRes.json('access_token'); 

    const hourMap = { 'none': '10', 'optimistic': '12', 'pessimistic': '14' };
    const hour = hourMap[STRATEGY] || '10';

    return {
        token: token,
        startsAt: `2026-11-15 ${hour}:00:00`,
        endsAt: `2026-11-15 ${parseInt(hour) + 2}:00:00`,
        roomId: 1 
    };
}

export default function (data) {
    const payload = JSON.stringify({
        room_id: data.roomId,
        title: `Concurrent Stress Test - ${STRATEGY}`,
        starts_at: data.startsAt,
        ends_at: data.endsAt
    });

    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${data.token}`,
            'X-Lock-Strategy': STRATEGY, 
        },
    };

    const res = http.post(`${BASE_URL}/reservations`, payload, params);

    bookingLatency.add(res.timings.duration);

    if (res.status === 201) {
        successfulBookings.add(1);
    } else if (res.status === 409) {
        conflictsDetected.add(1);
    } else if (res.status === 422) {
        console.error(`Validation Error: ${res.body}`);
    } else if (res.status >= 500) {
        databaseErrors.add(1);
    } else {
        // THIS WILL EXTRACT JUST THE ERROR MESSAGE AND HIDE THE GIANT STACK TRACE
        let shortError = "Unknown";
        try {
            shortError = res.json('message');
        } catch(e) {
            shortError = res.body.substring(0, 100) + "...";
        }
        console.error(`[HTTP ${res.status}] Error: ${shortError}`);
    }

    check(res, {
        'is status 201 or 409': (r) => r.status === 201 || r.status === 409,
    });
}