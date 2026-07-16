# Clinic API Documentation

**Base URL:** `http://your-domain.com/api`  
**Content-Type:** `application/json`  
**Accept:** `application/json`

---

## Authentication

This API uses **Bearer Token** authentication (Laravel Sanctum).

After login or register, you receive a `token`. For every protected endpoint, add this header:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## User Roles

| Role      | Description                          |
| --------- | ------------------------------------ |
| `patient` | Default role. Can book appointments. |
| `doctor`  | Can manage their own appointments.   |

---

## Error Responses

All errors follow this format:

```json
{
    "message": "Unauthenticated."
}
```

Validation errors (HTTP 422):

```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

---

---

# 1. AUTH ENDPOINTS

---

## 1.1 Register

**POST** `/api/register`  
**Auth Required:** No (Public)

### Request Body (JSON)

| Field                   | Type   | Required | Rules                                   |
| ----------------------- | ------ | -------- | --------------------------------------- |
| `full_name`             | string | Yes      | Max 255 characters                      |
| `email`                 | string | Yes      | Valid email, unique, max 255 characters |
| `password`              | string | Yes      | Min 8 characters                        |
| `password_confirmation` | string | Yes      | Must match `password`                   |

### Example Request

```json
{
    "full_name": "Ahmed Hassan",
    "email": "ahmed@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Success Response — HTTP 201

```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "full_name": "Ahmed Hassan",
        "email": "ahmed@example.com",
        "role": "patient",
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:00:00"
    },
    "token": "1|abcdefghij1234567890"
}
```

> **Note:** Save the `token` value — it is needed for all protected endpoints.

---

## 1.2 Login

**POST** `/api/login`  
**Auth Required:** No (Public)

### Request Body (JSON)

| Field      | Type   | Required | Rules       |
| ---------- | ------ | -------- | ----------- |
| `email`    | string | Yes      | Valid email |
| `password` | string | Yes      | Any string  |

### Example Request

```json
{
    "email": "ahmed@example.com",
    "password": "password123"
}
```

### Success Response — HTTP 200

```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "full_name": "Ahmed Hassan",
        "email": "ahmed@example.com",
        "role": "patient",
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:00:00"
    },
    "token": "2|xyz9876543210abcdef"
}
```

### Error Response — HTTP 401

```json
{
    "message": "Invalid credentials"
}
```

---

## 1.3 Logout

**POST** `/api/logout`  
**Auth Required:** Yes (any logged-in user)

### Headers

```
Authorization: Bearer YOUR_TOKEN_HERE
```

### Request Body

None

### Success Response — HTTP 200

```json
{
    "message": "Logged out successfully"
}
```

---

---

# 2. SPECIALTY ENDPOINTS

---

## 2.1 List All Specialties

**GET** `/api/specialties`  
**Auth Required:** Yes (patient or doctor)

### Headers

```
Authorization: Bearer YOUR_TOKEN_HERE
```

### Request Body

None

### Success Response — HTTP 200

```json
{
    "data": [
        {
            "id": 1,
            "name": "Cardiology",
            "doctors_count": 3,
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        },
        {
            "id": 2,
            "name": "Dermatology",
            "doctors_count": 2,
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        }
    ]
}
```

---

---

# 3. DOCTOR ENDPOINTS

---

## 3.1 List All Doctors

**GET** `/api/doctors`  
**Auth Required:** Yes (patient or doctor)

### Headers

```
Authorization: Bearer YOUR_TOKEN_HERE
```

### Query Parameters (Optional)

| Parameter      | Type    | Description                                       |
| -------------- | ------- | ------------------------------------------------- |
| `specialty_id` | integer | Filter doctors by specialty ID (exact match)      |
| `specialty`    | string  | Filter doctors by specialty name (partial search) |

### Example Requests

```
GET /api/doctors
GET /api/doctors?specialty_id=1
GET /api/doctors?specialty=cardio
```

### Success Response — HTTP 200

```json
{
    "data": [
        {
            "id": 1,
            "full_name": "Dr. Sarah Ali",
            "email": "sarah@clinic.com",
            "consultation_fee": "150.00",
            "specialty": {
                "id": 1,
                "name": "Cardiology",
                "created_at": "2025-01-01 10:00:00",
                "updated_at": "2025-01-01 10:00:00"
            },
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        }
    ]
}
```

---

## 3.2 Get Single Doctor

**GET** `/api/doctors/{id}`  
**Auth Required:** Yes (patient or doctor)

### Headers

```
Authorization: Bearer YOUR_TOKEN_HERE
```

### URL Parameter

| Parameter | Type    | Description     |
| --------- | ------- | --------------- |
| `id`      | integer | The doctor's ID |

### Example Request

```
GET /api/doctors/1
```

### Success Response — HTTP 200

```json
{
    "data": {
        "id": 1,
        "full_name": "Dr. Sarah Ali",
        "email": "sarah@clinic.com",
        "consultation_fee": "150.00",
        "specialty": {
            "id": 1,
            "name": "Cardiology",
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        },
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:00:00"
    }
}
```

### Error Response — HTTP 404

```json
{
    "message": "Doctor not found"
}
```

---

---

# 4. PATIENT ENDPOINTS

> These endpoints require the logged-in user to have the **`patient`** role.  
> A doctor trying to access these will get HTTP 403.

---

## 4.1 Book an Appointment

**POST** `/api/appointments`  
**Auth Required:** Yes — **Patient only**

### Headers

```
Authorization: Bearer PATIENT_TOKEN_HERE
```

### Request Body (JSON)

| Field              | Type     | Required | Rules                                  |
| ------------------ | -------- | -------- | -------------------------------------- |
| `doctor_id`        | integer  | Yes      | Must exist in the doctors table        |
| `appointment_date` | datetime | Yes      | Must be a future date/time (after now) |

### Example Request

```json
{
    "doctor_id": 1,
    "appointment_date": "2025-08-15 10:30:00"
}
```

### Success Response — HTTP 201

```json
{
    "message": "Appointment booked successfully",
    "appointment": {
        "id": 10,
        "patient": {
            "id": 1,
            "full_name": "Ahmed Hassan",
            "email": "ahmed@example.com"
        },
        "doctor": {
            "id": 1,
            "full_name": "Dr. Sarah Ali",
            "email": "sarah@clinic.com",
            "consultation_fee": "150.00",
            "specialty": {
                "id": 1,
                "name": "Cardiology"
            }
        },
        "appointment_date": "2025-08-15 10:30:00",
        "status": "pending",
        "doctor_notes": null,
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:00:00"
    }
}
```

### Error Response — HTTP 403 (if user is a doctor)

```json
{
    "message": "Forbidden. Required role: patient"
}
```

---

## 4.2 Get My Appointments

**GET** `/api/my-appointments`  
**Auth Required:** Yes — **Patient only**

### Headers

```
Authorization: Bearer PATIENT_TOKEN_HERE
```

### Request Body

None

### Success Response — HTTP 200

```json
{
    "appointments": [
        {
            "id": 10,
            "patient": null,
            "doctor": {
                "id": 1,
                "full_name": "Dr. Sarah Ali",
                "consultation_fee": "150.00",
                "specialty": { "id": 1, "name": "Cardiology" }
            },
            "appointment_date": "2025-08-15 10:30:00",
            "status": "pending",
            "doctor_notes": null,
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        }
    ],
    "upcoming": [
        {
            "id": 10,
            "doctor": { "id": 1, "full_name": "Dr. Sarah Ali" },
            "appointment_date": "2025-08-15 10:30:00",
            "status": "pending",
            "doctor_notes": null
        }
    ],
    "previous": []
}
```

> **Note:**
>
> - `appointments` → all appointments for this patient
> - `upcoming` → only `pending` or `confirmed` appointments
> - `previous` → only `completed` or `cancelled` appointments

---

---

# 5. DOCTOR ENDPOINTS

> These endpoints require the logged-in user to have the **`doctor`** role.  
> A patient trying to access these will get HTTP 403.

---

## 5.1 Get Today's Appointments

**GET** `/api/doctor/appointments`  
**Auth Required:** Yes — **Doctor only**

### Headers

```
Authorization: Bearer DOCTOR_TOKEN_HERE
```

### Request Body

None

### Success Response — HTTP 200

```json
{
    "appointments": [
        {
            "id": 10,
            "patient": {
                "id": 1,
                "full_name": "Ahmed Hassan",
                "email": "ahmed@example.com"
            },
            "doctor": null,
            "appointment_date": "2025-01-15 10:30:00",
            "status": "confirmed",
            "doctor_notes": null,
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        }
    ]
}
```

> Returns only appointments for **today's date**, sorted by time ascending.

---

## 5.2 Get All Appointments

**GET** `/api/doctor/appointments/all`  
**Auth Required:** Yes — **Doctor only**

### Headers

```
Authorization: Bearer DOCTOR_TOKEN_HERE
```

### Request Body

None

### Success Response — HTTP 200

```json
{
    "appointments": [
        {
            "id": 10,
            "patient": {
                "id": 1,
                "full_name": "Ahmed Hassan",
                "email": "ahmed@example.com"
            },
            "doctor": null,
            "appointment_date": "2025-08-15 10:30:00",
            "status": "pending",
            "doctor_notes": null,
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        }
    ]
}
```

> Returns **all** appointments for this doctor, sorted by date descending (newest first).

---

## 5.3 Update Appointment Status

**PUT** `/api/appointments/{id}/status`  
**Auth Required:** Yes — **Doctor only**

### Headers

```
Authorization: Bearer DOCTOR_TOKEN_HERE
```

### URL Parameter

| Parameter | Type    | Description        |
| --------- | ------- | ------------------ |
| `id`      | integer | The appointment ID |

### Request Body (JSON)

| Field    | Type   | Required | Allowed Values                                   |
| -------- | ------ | -------- | ------------------------------------------------ |
| `status` | string | Yes      | `pending`, `confirmed`, `completed`, `cancelled` |

### Example Request

```json
{
    "status": "confirmed"
}
```

### Success Response — HTTP 200

```json
{
    "message": "Appointment status updated successfully",
    "appointment": {
        "id": 10,
        "patient": {
            "id": 1,
            "full_name": "Ahmed Hassan",
            "email": "ahmed@example.com"
        },
        "doctor": {
            "id": 1,
            "full_name": "Dr. Sarah Ali",
            "consultation_fee": "150.00",
            "specialty": { "id": 1, "name": "Cardiology" }
        },
        "appointment_date": "2025-08-15 10:30:00",
        "status": "confirmed",
        "doctor_notes": null,
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:05:00"
    }
}
```

### Error Response — HTTP 404

```json
{
    "message": "Appointment not found"
}
```

---

## 5.4 Update Appointment Notes

**PUT** `/api/appointments/{id}/notes`  
**Auth Required:** Yes — **Doctor only**

### Headers

```
Authorization: Bearer DOCTOR_TOKEN_HERE
```

### URL Parameter

| Parameter | Type    | Description        |
| --------- | ------- | ------------------ |
| `id`      | integer | The appointment ID |

### Request Body (JSON)

| Field          | Type   | Required | Rules               |
| -------------- | ------ | -------- | ------------------- |
| `doctor_notes` | string | Yes      | Max 5000 characters |

### Example Request

```json
{
    "doctor_notes": "Patient has high blood pressure. Prescribed medication X. Follow up in 2 weeks."
}
```

### Success Response — HTTP 200

```json
{
    "message": "Medical notes updated successfully",
    "appointment": {
        "id": 10,
        "patient": {
            "id": 1,
            "full_name": "Ahmed Hassan",
            "email": "ahmed@example.com"
        },
        "doctor": {
            "id": 1,
            "full_name": "Dr. Sarah Ali",
            "consultation_fee": "150.00",
            "specialty": { "id": 1, "name": "Cardiology" }
        },
        "appointment_date": "2025-08-15 10:30:00",
        "status": "confirmed",
        "doctor_notes": "Patient has high blood pressure. Prescribed medication X. Follow up in 2 weeks.",
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:10:00"
    }
}
```

### Error Response — HTTP 404

```json
{
    "message": "Appointment not found"
}
```

---

---

# QUICK REFERENCE TABLE

| #   | Method | Endpoint                        | Auth         | Role    | Description                   |
| --- | ------ | ------------------------------- | ------------ | ------- | ----------------------------- |
| 1   | POST   | `/api/register`                 | None         | —       | Register a new patient        |
| 2   | POST   | `/api/login`                    | None         | —       | Login and get token           |
| 3   | POST   | `/api/logout`                   | Bearer Token | Any     | Logout (delete token)         |
| 4   | GET    | `/api/specialties`              | Bearer Token | Any     | List all specialties          |
| 5   | GET    | `/api/doctors`                  | Bearer Token | Any     | List all doctors (filterable) |
| 6   | GET    | `/api/doctors/{id}`             | Bearer Token | Any     | Get single doctor details     |
| 7   | POST   | `/api/appointments`             | Bearer Token | Patient | Book an appointment           |
| 8   | GET    | `/api/my-appointments`          | Bearer Token | Patient | Get my appointments           |
| 9   | GET    | `/api/doctor/appointments`      | Bearer Token | Doctor  | Get today's appointments      |
| 10  | GET    | `/api/doctor/appointments/all`  | Bearer Token | Doctor  | Get all appointments          |
| 11  | PUT    | `/api/appointments/{id}/status` | Bearer Token | Doctor  | Update appointment status     |
| 12  | PUT    | `/api/appointments/{id}/notes`  | Bearer Token | Doctor  | Update medical notes          |

---

# APPOINTMENT STATUS FLOW

```
pending  →  confirmed  →  completed
pending  →  cancelled
confirmed  →  cancelled
```

| Status      | Meaning                               |
| ----------- | ------------------------------------- |
| `pending`   | Just booked, waiting for confirmation |
| `confirmed` | Doctor confirmed the appointment      |
| `completed` | Appointment was completed             |
| `cancelled` | Appointment was cancelled             |

---

# NOTES FOR FRONTEND

1. **Always send** `Content-Type: application/json` and `Accept: application/json` headers on every request.
2. **Store the token** in localStorage or a cookie after login/register. Clear it on logout.
3. **Use the `role` field** from the user object to show/hide UI sections (patient dashboard vs doctor dashboard).
4. **Date format** for `appointment_date`: `YYYY-MM-DD HH:MM:SS` — the date must be in the future.
5. **Validation errors** always come in `errors` object with field names as keys and array of error messages as values.
6. **HTTP 401** means the token is missing or expired — redirect to login.
7. **HTTP 403** means the user's role is not allowed — show an access denied message.
8. **HTTP 422** means validation failed — display the errors next to the form fields.
