http://127.0.0.1:8000/api/employee/party

Request:
{"company_id":5}

Response:
{
    "list": [
        {
            "partyId": 4,
            "strPartyName": "ancd",
            "iCompanyId": 5,
            "address1": "Sola",
            "address2": "Science City",
            "address3": null,
            "strGST": "121145",
            "iMobile": "09987654321",
            "strEmail": "dev1.apolloinfotech@gmail.com",
            "strIP": "127.0.0.1",
            "strEntryDate": "2025-10-25",
            "iStatus": 1,
            "isDelete": 0,
            "created_at": "2025-10-25T10:31:50.000000Z",
            "updated_at": "2025-10-25T10:31:50.000000Z",
            "company": {
                "company_id": 5,
                "company_name": "Apollo Infotech",
                "GST": null,
                "contact_person_name": "Krunal Shah",
                "mobile": "9824773136",
                "email": "shahkrunal83@gmail.com",
                "Address": "Maninagar",
                "pincode": 380008,
                "city": "Ahmedabad",
                "state_id": 1,
                "password": "$2y$10$wtwp5bVe4tu8NclL638G9.C2KZwBZDw.GiXv4leiiFAC9i2faEPH2",
                "subscription_start_date": "2025-09-26 17:36:00",
                "subscription_end_date": "2026-09-26 17:36:00",
                "plan_id": 4,
                "plan_amount": "5000",
                "plan_days": 365,
                "delivery_terms": "apollo delivery terms",
                "payment_terms": "apollo payment terms",
                "terms_condition": "<p>test term and conditions</p>",
                "iStatus": 1,
                "isDeleted": 0,
                "created_at": "2025-07-03T05:28:23.000000Z",
                "updated_at": "2025-10-25T11:10:48.000000Z"
            }
        },
        {
            "partyId": 1,
            "strPartyName": "Apollo",
            "iCompanyId": 5,
            "address1": "test address 1",
            "address2": "test address 2",
            "address3": "test address 3",
            "strGST": null,
            "iMobile": "9874589874",
            "strEmail": "test@gmail.com",
            "strIP": null,
            "strEntryDate": "-0001-11-30",
            "iStatus": 1,
            "isDelete": 0,
            "created_at": null,
            "updated_at": null,
            "company": {
                "company_id": 5,
                "company_name": "Apollo Infotech",
                "GST": null,
                "contact_person_name": "Krunal Shah",
                "mobile": "9824773136",
                "email": "shahkrunal83@gmail.com",
                "Address": "Maninagar",
                "pincode": 380008,
                "city": "Ahmedabad",
                "state_id": 1,
                "password": "$2y$10$wtwp5bVe4tu8NclL638G9.C2KZwBZDw.GiXv4leiiFAC9i2faEPH2",
                "subscription_start_date": "2025-09-26 17:36:00",
                "subscription_end_date": "2026-09-26 17:36:00",
                "plan_id": 4,
                "plan_amount": "5000",
                "plan_days": 365,
                "delivery_terms": "apollo delivery terms",
                "payment_terms": "apollo payment terms",
                "terms_condition": "<p>test term and conditions</p>",
                "iStatus": 1,
                "isDeleted": 0,
                "created_at": "2025-07-03T05:28:23.000000Z",
                "updated_at": "2025-10-25T11:10:48.000000Z"
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 2,
        "last_page": 1
    },
    "filters": {
        "company_id": 5,
        "q": null
    }
}