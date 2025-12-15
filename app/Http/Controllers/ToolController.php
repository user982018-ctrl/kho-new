<?php
namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Group;
use App\Models\Orders;
use App\Models\SaleCare;
use App\Models\SrcPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TestController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShippingOrderController;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use App\Exports\UsersExport;
use App\Models\User;
use App\Models\GroupUser;
use App\Models\DetailUserGroup;


class ToolController extends Controller
{
    const COOKI_USU2 = 'auth_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwMTlhOWI3NS1lZTRkLTdhYzUtNjU5OS05NDIzNGEwZGU1MGQiLCJ1c2VybmFtZSI6ImFkbWluIiwiZnVsbE5hbWUiOiJBZG1pbiIsInJvbGVzIjpbIkFkbWluIiwiTmjDom4gdmnDqm4iXSwicGVybWlzc2lvbnMiOlsibWFpbi5tYXJrZXRpbmcucGFnZS13ZWxjb21lLW1lc3NhZ2UuY3JlYXRlIiwibWFpbi5tYXJrZXRpbmcucGFnZS13ZWxjb21lLW1lc3NhZ2UucmVhZCIsIm1haW4ubWFya2V0aW5nLnBhZ2Utd2VsY29tZS1tZXNzYWdlLnVwZGF0ZSIsIm1haW4ubWFya2V0aW5nLnBhZ2Utd2VsY29tZS1tZXNzYWdlLmRlbGV0ZSIsIm1haW4ubWFya2V0aW5nLnRlbXBsYXRlLWNhbXBhaWduLmNyZWF0ZSIsIm1haW4ubWFya2V0aW5nLnRlbXBsYXRlLWNhbXBhaWduLnJlYWQiLCJtYWluLm1hcmtldGluZy50ZW1wbGF0ZS1jYW1wYWlnbi51cGRhdGUiLCJtYWluLm1hcmtldGluZy50ZW1wbGF0ZS1jYW1wYWlnbi5kZWxldGUiLCJtYWluLnN5c3RlbS51c2VyLmNyZWF0ZSIsIm1haW4uc3lzdGVtLnVzZXIucmVhZCIsIm1haW4uYWNjb3VudCIsIm1haW4uYWNjb3VudC5jcmVhdGUiLCJtYWluLmFjY291bnQucmVhZCIsIm1haW4uYWNjb3VudC51cGRhdGUiLCJtYWluLmFjY291bnQuZGVsZXRlIiwibWFpbi5zeXN0ZW0udXNlci51cGRhdGUiLCJtYWluLnN5c3RlbS51c2VyLmRlbGV0ZSIsIm1haW4uc3lzdGVtLnJvbGUuY3JlYXRlIiwibWFpbi5zeXN0ZW0ucm9sZS5yZWFkIiwibWFpbi5zeXN0ZW0ucm9sZS51cGRhdGUiLCJhZG1pbi5zeXN0ZW0iLCJhZG1pbi5zeXN0ZW0uY29uZmlnIiwibWFpbi5kYXNoYm9hcmQiLCJtYWluLm5vdGlmaWNhdGlvbiIsIm1haW4uYXV0b21hdGlvbiIsIm1haW4uc3lzdGVtLnJvbGUuZGVsZXRlIiwibWFpbi5tYXJrZXRpbmcuYWQtY2hhbm5lbHMuY3JlYXRlIiwibWFpbi5tYXJrZXRpbmcuYWQtY2hhbm5lbHMucmVhZCIsIm1haW4ubWVkaWEiLCJtYWluLm1hcmtldGluZy5hZC1jaGFubmVscy51cGRhdGUiLCJtYWluLm1hcmtldGluZy5hZC1jaGFubmVscy5kZWxldGUiLCJtYWluLm1hcmtldGluZy5hZHMtbWV0cmljLmNyZWF0ZSIsIm1haW4ubWFya2V0aW5nLmFkcy1tZXRyaWMucmVhZCIsIm1haW4ubWFya2V0aW5nLmFkcy1tZXRyaWMudXBkYXRlIiwibWFpbi5tYXJrZXRpbmcuYWRzLW1ldHJpYy5kZWxldGUiLCJhZG1pbi5sb2cuYWQtYWN0aW9uLnJlYWQiLCJtYWluLmF1dG9tYXRpb24uY3JlYXRlIiwibWFpbi5hdXRvbWF0aW9uLnJlYWQiLCJtYWluLmF1dG9tYXRpb24udXBkYXRlIiwibWFpbi5hdXRvbWF0aW9uLmRlbGV0ZSIsImFkbWluLmxvZy5hZHMtY3JlYXRvci1iYXRjaC5yZWFkIiwiYWRtaW4ubG9nLndvcmtmbG93LnJlYWQiLCJtYWluLnJlcG9ydC50ZWxlc2FsZXMtdGFzay1tYXJrZXRpbmciLCJtYWluLnJlcG9ydC50ZWxlc2FsZXMtdGFzay1zYWxlIiwibWFpbi5yZXBvcnQudGVsZXNhbGVzLXRhc2siLCJtYWluLm1lZGlhLmNyZWF0ZSIsIm1haW4ubWVkaWEucmVhZCIsIm1haW4ubWVkaWEudXBkYXRlIiwibWFpbi5tZWRpYS5kZWxldGUiLCJtYWluLmRhc2hib2FyZC5mYWNlYm9vayIsIm1haW4uZGFzaGJvYXJkLmFkcy1tYW5hZ2VyIiwibWFpbi5ub3RpZmljYXRpb24ucmVhZCIsIm1haW4ucmVwb3J0LmFkLXNwZW5kIiwibWFpbi5wcm9kdWN0IiwibWFpbi5vcmRlciIsIm1haW4udGVsZXNhbGUiLCJtYWluLnRlbGVzYWxlLndvcmtmbG93IiwibWFpbi50ZWxlc2FsZS50YXNrIiwibWFpbi50ZWxlc2FsZS51c2VyIiwibWFpbi5yZXBvcnQiLCJtYWluLm9yZGVyLmxpc3QiLCJtYWluLmN1c3RvbWVyLmxpc3QiLCJtYWluLm9yZGVyLnJlcG9ydCIsIm1haW4ucHJvZHVjdC5jYXRlZ29yeSIsIm1haW4ucHJvZHVjdC5saXN0IiwibWFpbi5tYXJrZXRpbmcuYWQtY2hhbm5lbHMiLCJtYWluLm1hcmtldGluZy5hZHMtY3JlYXRvciIsIm1haW4ubWFya2V0aW5nLmFkcy1tZXRyaWMiLCJtYWluLm1hcmtldGluZy5wYWdlLXdlbGNvbWUtbWVzc2FnZSIsIm1haW4ubWFya2V0aW5nLnRlbXBsYXRlLWNhbXBhaWduIiwibWFpbi5zeXN0ZW0ucm9sZSIsIm1haW4uc3lzdGVtLnVzZXIiLCJtYWluLnN5c3RlbS51c2VyLWdyb3VwIiwiYWRtaW4ubG9nIiwiYWRtaW4ubG9nLmFkLWFjdGlvbiIsImFkbWluLmxvZy5hZHMtY3JlYXRvci1iYXRjaCIsImFkbWluLmxvZy53b3JrZmxvdyIsIm1haW4ubWVzc2FnaW5nIiwibWFpbi5tZXNzYWdpbmcuY2hhbm5lbHMiLCJtYWluLm1lc3NhZ2luZy5jaGF0IiwibWFpbi5tZXNzYWdpbmcubXVsdGktY2hhdCIsIm1haW4uc3lzdGVtIiwibWFpbi5tYXJrZXRpbmciLCJtYWluLnJlcG9ydC51c2VyLWNoYXQtc3RhdGlzdGljIl0sImlhdCI6MTc2NTcxOTIwNSwiZXhwIjoxNzY1ODA1NjA1fQ.je3Lclof-fUDj4VhJ54VjXGnGp5gUzGUBPo21FwgJYM';
    const CHANNEL_IDS = [
        "019a9b88-0281-783d-70c4-284646c2c153",
        "019a9b88-0291-75d3-2859-26acaecc63c3",
        "019a9b88-0298-7376-dcd3-b9bc9b86c659",
        "019a9b88-02a4-7680-5e24-2ef564fb8a9e",
        "019a9b88-02ab-7bae-55e9-d2bb8cc5d328",
        "019a9b88-02b5-7cd1-0bb9-78185da50a91",
        "019a9b88-02c4-7363-7c84-21a1f6a5f318",
        "019a9b88-02ca-72ed-4a5f-1458c5c268f0",
        "019a9b88-02cc-7995-7ab7-62d8c69d1cfa",
        "019a9b88-02d4-7b81-6b52-f310c6a1a0ff",
        "019a9b88-02da-7740-afd4-28a2f9c5db32",
        "019a9b88-0300-7062-9a5f-bc6d09c20742",
        "019a9b88-0301-7f6d-0cd8-670f2b9d4b22",
        "019a9b88-0303-7019-4ff9-4993482b8c47",
        "019a9b88-0303-747b-827d-bc64fa3dbe3e",
        "019a9b88-0306-796d-3014-4f859923e4b3",
        "019a9b88-0307-7e78-b38b-6eb2e1bdfddd",
        "019a9b88-030d-7d0e-60e2-8d6da16249e2",
        "019a9b88-0318-7a03-10fa-2cb1e70d4c38",
        "019a9b88-031e-7297-42f0-db2882650e7e",
        "019a9b88-128a-71ee-b20d-20a3e41baa52",
        "019a9b88-128b-77ea-6b61-2ea885bd20c8",
        "019a9b88-128b-7a63-224f-b078965974a2",
        "019a9b88-128c-7051-4432-6ae6dc530a14",
        "019a9b88-128c-7d33-b917-38305ab70c5d",
        "019a9b88-128d-7473-d93d-fcf18a535db5",
        "019a9b88-128d-7e82-ebbc-ad9ec52248e1",
        "019a9b88-128e-7076-fed3-d0d8a0f2609a",
        "019a9b88-128e-77a6-e288-760da8434357",
        "019a9b88-1292-7d8c-ed83-c8c63509c0f2",
        "019a9b88-129a-727c-5e9b-4faf7f367412",
        "019a9b88-129a-7794-e42e-0de0e3a63274",
        "019a9b88-129b-75db-b42d-b5572429b3a6",
        "019a9b88-129b-7b0e-7f81-f20ce0c60208",
        "019a9b88-129c-7681-21ff-dce709d56a65",
        "019a9b88-129c-7cd1-9421-9b5b25b7b12e",
        "019a9b88-129d-756f-7b1a-5a0d93c28c2f",
        "019a9b88-129d-7e25-9fe1-c0070190c3dc",
        "019a9b88-129e-797c-5ec8-738473c3e95d",
        "019a9b88-12ab-7101-0c06-355de79f81e3",
        "019a9b88-1b0d-7619-40a7-3281c84045a6",
        "019a9b88-1b31-7453-bd21-96e48e2cd3dc",
        "019a9b88-1b35-782e-c082-30688e0c3318",
        "019a9b88-1b37-7e13-e91b-2c7d1a60f440",
        "019a9b88-1b38-7d43-1482-e4439618bfa1",
        "019a9b88-1b38-7ded-4db9-aea8926bedcc",
        "019a9b88-1b48-7d66-b5fa-15fd490c70f9",
        "019a9b88-1b49-7cdd-4320-8739d0a9cee9",
        "019a9b88-1b53-77a3-8f22-8b71df59b7d9",
        "019a9b88-1b5f-77ad-5a17-8700b5c3c78e",
        "019a9b88-1b6b-7b39-e582-8677b2ed857d",
        "019a9b88-1b6e-771e-5619-06d2e6c8cad8",
        "019a9b88-1b70-76ad-b2a6-8ed53c9f1818",
        "019a9b88-1b71-7278-c788-43c3b6d0e2d7",
        "019a9b88-1b77-7676-516b-45273483e0a2",
        "019a9b88-1b95-7efd-4442-e1aefab89e9e",
        "019a9b88-1b9a-766f-06b9-b2ae052daed7",
        "019a9b88-1b9c-7493-0be5-7a42a29eecd0",
        "019a9b88-1ba1-7f62-2fbb-93ad82cb71fa",
        "019a9b88-1ba2-7b52-8370-e699ae5c49db",
        "019a9b88-2358-717a-8a50-8c52b341f17b",
        "019a9b88-2359-7594-becc-510978d2f762",
        "019a9b88-2359-77bf-c23e-c140ea02d222",
        "019a9b88-2359-7ed8-64f3-6152af6a3031",
        "019a9b88-235a-7439-2412-62787ea93ad4",
        "019a9b88-235b-71a3-bf73-d17d467f3715",
        "019a9b88-235b-7993-836f-075139a19522",
        "019a9b88-235c-7e1c-688f-a898bbf7400f",
        "019a9b88-2360-7b7d-b75d-bc829b4de2b1",
        "019a9b88-2362-7a17-2766-e8154a85de46",
        "019a9b88-2362-7fa1-b4c3-780968e373df",
        "019a9b88-2363-7de6-fcfb-2bb89e7b259e",
        "019a9b88-2364-7646-2f6b-b8bad450f9fd",
        "019a9b88-2364-7a75-ae5d-d033e543cc0d",
        "019a9b88-2365-7d3f-12a0-20d24b3578f6",
        "019a9b88-2367-7b67-033b-e2e481c24448",
        "019a9b88-2368-7c8b-f346-8051b74e69ae",
        "019a9b88-236c-7218-2985-dffc5fb8283c",
        "019a9b88-236e-78ec-e77c-8ebbc94f1215",
        "019a9b88-236e-7c8f-0c3f-2e130f037080",
        "019a9b88-2b0d-7b92-a363-bb9df969f0b5",
        "019a9b88-2b0f-7481-8bad-6d414928dae9",
        "019a9b88-2b0f-7a94-e095-a5d644f40d30",
        "019a9b88-2b10-7857-d4ce-26d08ab7ab10",
        "019a9b88-2b11-753e-3be3-06cb588be90f",
        "019a9b88-2b12-76e7-1300-7deb2b1ea397",
        "019a9b88-2b12-7e3d-b736-d7a00ece87c7",
        "019a9b88-2b13-7842-1abf-1281900e07eb",
        "019a9b88-2b15-7949-2ca8-20b6509670f9",
        "019a9b88-2b18-711e-ea53-ef93cae359f3",
        "019a9b88-2b1b-75dc-89c5-3a09303beaea",
        "019a9b88-2b1b-7782-42ac-e2415c583c2e",
        "019a9b88-2b1c-7ae2-713c-f44ba573e076",
        "019a9b88-2b21-7075-685d-902c29bb5551",
        "019a9b88-2b21-78c0-77af-497461710a96",
        "019a9b88-2b22-7253-2c9e-0d160f81909d",
        "019a9b88-2b23-77c8-49e7-c7d96cf4a337",
        "019a9b88-2b2c-7fb6-b686-e7c1b494793e",
        "019a9b88-2b2d-7054-d49f-4f377baf3f2f",
        "019a9b88-2b32-7c4d-2ffd-30c23c8a68d3",
        "019a9b88-3450-71ee-06c0-8dad7e49d25c",
        "019a9b88-3451-7215-44ac-d670e1ab0dfa",
        "019a9b88-3451-79bc-4dcc-5db16cd5ed63",
        "019a9b88-3452-7922-0e40-0796a9fa7385",
        "019a9b88-3452-7aff-5ea4-8e39cd1a06e2",
        "019a9b88-3453-7039-5721-838ac29a4758",
        "019a9b88-3453-710e-7791-3d9a8ee7b824",
        "019a9b88-3453-7a79-3a29-216f6c6a2fcf",
        "019a9b88-3455-7b7f-03b5-20f3216656ab",
        "019a9b88-3459-7d7d-f493-3d66a4ec3d8a",
        "019a9b88-345f-764f-f0cf-691686744d1a",
        "019a9b88-3460-78f5-0e8c-4469cb5f5af7",
        "019a9b88-3461-7886-599f-a12bc8a56db7",
        "019a9b88-3462-7dd2-cbf8-d4b1c8a671cc",
        "019a9b88-3463-733e-b181-14550350c540",
        "019a9b88-3463-7d43-8c38-4962cc241c6f",
        "019a9b88-3464-78f8-ea24-a709500d378e",
        "019a9b88-3465-795a-8292-08d0366b26c6",
        "019a9b88-3467-7855-e154-d49932289946",
        "019a9b88-3470-7615-22c6-934b73491b39",
        "019a9b88-3e00-7367-d27f-8025603693e0",
        "019a9b88-3e00-7a97-8eae-6e7634e02f8a",
        "019a9b88-3e00-7adf-cf9a-dfcb4652c868",
        "019a9b88-3e01-78f8-6e9e-5b3bb7fadc3e",
        "019a9b88-3e02-733b-25b3-cb9ec85b0f91",
        "019a9b88-3e02-7383-d340-c69e2530336f",
        "019a9b88-3e02-78ef-0648-90d6c57826a5",
        "019a9b88-3e03-7c0d-c1cf-7d6c18e020f4",
        "019a9b88-3e04-7848-fb2c-0e374b8bbc48",
        "019a9b88-3e07-7e2b-f0b5-272e613624c7",
        "019a9b88-3e09-7697-3913-d1907c0595df",
        "019a9b88-3e0a-7759-c7f1-db7926d4226c",
        "019a9b88-3e0a-7fbf-623f-7406ab1b001f",
        "019a9b88-3e0c-71ff-ca2f-fa13f08d0aa3",
        "019a9b88-3e0c-74a2-0a14-379efd4daab1",
        "019a9b88-3e0c-7aa7-9fa7-a0881afc5c13",
        "019a9b88-3e0d-7441-358e-ffef2fd3d6df",
        "019a9b88-3e0e-7aa8-fbf7-75961862db07",
        "019a9b88-3e19-7aca-632d-72d5e37c7942",
        "019a9b88-3e1a-7356-0902-e78ffb41510a",
        "019a9b88-4687-7271-b5bd-7389a9e7561a",
        "019a9b88-4687-7495-4bc1-2cec47114323",
        "019a9b88-4688-7333-5c9d-b292e41a6fa6",
        "019a9b88-4689-71e2-30c9-79a23aeb5b10",
        "019a9b88-4689-7e86-4de8-d06b7ea8dc11",
        "019a9b88-468a-7379-06eb-90a060f5ba57",
        "019a9b88-468b-754d-e7ab-435b09a9502c",
        "019a9b88-468c-7045-067b-cb82b039644a",
        "019a9b88-4692-7009-1fa9-df8e635202c0",
        "019a9b88-4693-75c5-5a98-70f50ad22b11",
        "019a9b88-469a-7752-3e58-c472a5861056",
        "019a9b88-469b-734b-6850-9ce270da3fea",
        "019a9b88-469b-79d9-b850-27da67030ac9",
        "019a9b88-469c-729b-1c22-5aaf888363af",
        "019a9b88-469c-7ae5-2566-9d0bb27f4eb6",
        "019a9b88-469d-75f2-bab3-33daa8833f2a",
        "019a9b88-469d-7bf0-c8b0-62abe857f490",
        "019a9b88-469e-7601-2829-4f0c8b722caa",
        "019a9b88-46a9-7c7b-dba0-efc5cb24c92c",
        "019a9b88-46ab-7b1f-45b2-a79d0e191501",
        "019a9b88-4ee6-7bef-f894-2279507d3f74",
        "019a9b88-4ee7-727c-0c7f-887251b3f5f2",
        "019a9b88-4ee7-7f88-dfc7-eb3a44254204",
        "019a9b88-4ee8-7954-9aad-6caf96fcabe4",
        "019a9b88-4ee8-7a63-d0a6-e7d5a75e970d",
        "019a9b88-4ee9-70f3-2320-961912145b4e",
        "019a9b88-4eea-78a2-bc0e-1595d50ad0e3",
        "019a9b88-4eea-79f4-8b59-55d817a645eb",
        "019a9b88-4eeb-7864-9d65-42b62f035ed4",
        "019a9b88-4eee-7855-f6e6-925f12e44e3b",
        "019a9b88-4ef5-7566-9598-6030b6335f3b",
        "019a9b88-4ef5-7a12-2e18-9ad8511157c4",
        "019a9b88-4ef6-7623-9b8a-a8da1c01941b",
        "019a9b88-4ef7-7307-0fcb-73a4f50411bc",
        "019a9b88-4ef7-7b55-b457-8ecd7b242083",
        "019a9b88-4ef8-7b6b-9e8a-f923e2b354cb",
        "019a9b88-4ef9-70b0-6042-ceb2401445c4",
        "019a9b88-4ef9-7f1b-35fb-dfd3ad9f4679",
        "019a9b88-4efc-77f7-c1d2-e944e75dbbdb",
        "019a9b88-4f04-7be3-6d60-9f9acfd1d620",
        "019a9b88-5820-7089-c31a-8aa676e7048e",
        "019a9b88-5820-78f7-0710-c24f7fc2e6e8",
        "019a9b88-5821-7251-809f-b2eb4a44efb9",
        "019a9b88-5821-7a51-d0a2-c8bfa67f0afe",
        "019a9b88-5822-7b4b-654b-173596e4baa2",
        "019a9b88-5822-7fb8-f2c9-eab3b691259c",
        "019a9b88-5825-7d1d-9cb4-b4f064027a98",
        "019a9b88-5826-7ada-761b-c95149703c75",
        "019a9b88-5828-7b8b-1cdb-b89a08d4b88e",
        "019a9b88-5829-7b6e-0bf1-3c4f0313e16b",
        "019a9b88-582e-7889-7b78-c2d4c595c6d5",
        "019a9b88-582e-7c4a-6962-44a5dab77c24",
        "019a9b88-582f-7c76-139b-bb02c1966c9c",
        "019a9b88-582f-7f61-db78-20cf8bf6427f",
        "019a9b88-5830-74b6-7c28-62d26572ed78",
        "019a9b88-5830-78b9-591a-b69b48b3ae27",
        "019a9b88-5831-795a-e976-7f618a2a1056",
        "019a9b88-5831-7e55-5e88-3bd4a815cd41",
        "019a9b88-5838-7ecd-ba20-a1b2454b2d37",
        "019a9b88-583b-7af9-21b2-5efd60edf6ad",
        "019a9b88-6058-7ca8-416b-d786da5b5414",
        "019aaa5a-caca-7d7d-9966-5a0ec9b6c37c",
        "019aaa5a-cae0-7e19-be7f-5f3a26ce9747",
        "019ab3b7-86cc-76fe-1964-610d08b7dc0e",
        "019ab3b7-86cd-7e4a-68ba-d26ef6e46ec5",
        "019ab3b7-8717-7712-ef06-8e2282f2a280",
        "019ab3b7-8717-79f8-fc91-79dd84b1aa4f",
        "019ab3b7-8718-78b5-9ef7-2f4ee1469ef9",
        "019ab3b7-871a-798c-10ce-de351969bd57",
        "019abf54-e07b-7fd7-dab7-1381a0abc799",
        "019abf5b-7a10-73a6-452b-5c8265f7db38",
        "019abf69-d99f-7694-4fa0-0fdc6a5756a2",
        "019ac364-7a3d-7be4-db40-486ad1df7264",
        "019ae86b-6578-7295-10af-103abc7e9074",
        "019ae86b-6608-7076-910a-9a1211e629b5",
        "019ae86b-6608-720c-ccce-2e49750f02cd",
        "019ae86b-6608-7222-6c45-291005add7b2",
        "019ae86b-6609-771a-24a1-f9d94deac306",
        "019ae86b-660b-7765-d9c0-8325bdd25803",
        "019ae86b-660b-7b19-ce0a-5d8196c85038",
        "019ae86b-660e-728d-8c9b-840549fb0c03",
        "019ae86b-660e-7310-66ea-6b1dd39e741a",
        "019ae86b-660f-707e-a2bd-ae9abb669128",
        "019ae86b-747a-74cc-0d55-b013b6046778",
        "019ae888-4b1a-7d03-de34-ce26434578de",
        "019ae888-4b24-7c09-2b4d-7e54033c5c54",
        "019ae888-4b24-7f85-c79a-5a65d7bcb7ae",
        "019aed63-a61a-7a14-16da-de0738b0b5fa",
        "019aed82-4780-7bd3-f1cd-bfbed7bcc31c",
        "019aed82-4783-7e88-be68-fe31db3b8a30",
        "019aedbc-ca24-723c-5696-90319c0f10d6",
        "019aedbc-ca2c-7c85-4510-da37af946ec9",
        "019aedca-2821-7e17-b185-25f6797008b6",
        "019b0216-362e-7d93-fab7-c304663839ac",
        "019b021d-1277-7059-21aa-31b25a809fde",
        "019b0366-f29b-7407-eb02-9eee15386e65",
        "019b0c6b-e77e-7c6d-cf5d-9eb127d3d325",
        "019b0cd8-a993-7d95-e6a7-f5789634ebab"
      ];
    public function getU2Data(){
        $fromDate = date('Y-m-d H:i:s', strtotime('-1 day'));
        $toDate = date('Y-m-d H:i:s');

        // "fromDate" => "2025-12-05T17:00:00.000Z",
        // "toDate" => "2025-12-06T11:37:04.855Z",
        $endpoint = 'https://ads.phanbonmiennam.net/backend/conversations/search';
        $data = [
            "limit" => 100,
            "page" => 1,
            "hasPhone" => true,
            // "channelIds" => self::CHANNEL_IDS,
            "fromDate" => $fromDate . 'Z',
            "toDate" => $toDate . 'Z',
            "id" => "019b1d1a-2a1b-72cd-2cc4-93893e51719a",
            "type" => "group",

        ];
        
        $response = Http::withHeaders([
            'Cookie' => self::COOKI_USU2,
        ])->post($endpoint, $data);

        $response = $response->json();
        if (isset($response['success']) && !$response['success']) {
            echo $response['message'] . '<br>';
            return ;
        }
       
        if ($response['total'] > 0) {
            foreach ($response['data'] as $item) {
                if ($item['conversationType'] == 'comment') {
                    $phone = $item['phoneNumbers'][0]['national'];
                    $phone = Helper::getCustomPhoneNum($phone);
                    $mId = $item['id'];
                    $is_duplicate = $hasOldOrder = $isOldCustomer = $assgin_user = 0;
                    $checkSaleCareOld = Helper::checkOrderSaleCarebyPhoneV5($phone, $mId, $is_duplicate, $hasOldOrder);
                    $typeCSKH = 1;
                    $name = $item['userName'];
                    if ($checkSaleCareOld && $name != '') {
                        $idPage = $item['channel']['providerId'];
                        $src = SrcPage::where('id_page', $idPage)->first();
                        if (!$src) {
                          continue;
                        }
                        $group = $src->group;
                        if (!$group) {
                          continue;
                        }
                        $linkPage = $src->link;
                        $namePage = $src->name;
                        $messages = '';
                        $chatId = $group->tele_hot_data;
                        $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldCustomer);
                        // dd($assignSale);
                        if (!$assignSale) {
                          continue;
                        }
        
                        if ($isOldCustomer == 1) {
                          $chatId = $group->tele_cskh_data;
                        }
        
                        $assgin_user = $assignSale->id;
                        $is_duplicate = ($is_duplicate) ? 1 : 0;
                        $sale = new SaleController();
                        $data = [
                          'page_link' => $linkPage,
                          'page_name' => $namePage,
                          'sex'       => 0,
                          'old_customer' => $isOldCustomer,
                          'address'   => '',
                          'messages'  => $messages,
                          'name'      => $name,
                          'phone'     => $phone,
                          'page_id'   => $idPage,
                          'text'      => 'Page ' . $namePage,
                          'chat_id'   => $chatId,
                          'm_id'      => $mId,
                          'assgin'    => $assgin_user,
                          'is_duplicate' => $is_duplicate,
                          'group_id'  => $group->id,
                          'has_old_order'  => $hasOldOrder,
                          'src_id'  => $src->id,
                          'type_TN' => $typeCSKH, 
                        ];
                        echo $phone . ' - ' . $name . '<br>';
                        
                        $request = new \Illuminate\Http\Request();
                        $request->replace($data);
                        $sale->save($request);
                    }
                }
            }
        }
    }
    public function trung(){
        // Join SaleCare với Orders và filter theo điều kiện
        $list = SaleCare::join('orders', 'sale_care.id_order_new', '=', 'orders.id')
            ->where('sale_care.group_id', 14)
            ->whereDate('orders.created_at', '>=', '2025-10-01')
            ->whereDate('orders.created_at', '<=', '2025-11-30')
            ->select('sale_care.*', 'orders.*')
            ->get();
        $i = 1;
        foreach ($list as $item) {
            echo $i . ' - ' . $item->phone . ' - ' . $item->full_name . ' - ' . $item->user->real_name. '<br>';
            $i++;
        }
    }
    public function truc(){
        // Bước 1: Tìm các Group có leader id = 161
        // lead_sale có thể là số hoặc JSON array, sử dụng cách tương tự Helper
        $allGroups = Group::get();
        $targetLeadId = 161;
        
        // $groups = $allGroups->filter(function ($group) use ($targetLeadId) {
        //     $leadSaleField = $group->lead_sale;
        //     $leadIds = $this->normalizeLeadSaleField($leadSaleField);
        //     if (empty($leadIds)) {
        //         return false;
        //     }
        //     return in_array($targetLeadId, $leadIds);
        // });
        
        // if ($groups->isEmpty()) {
        //     return response()->json([
        //         'message' => 'Không tìm thấy Group nào có leader id = 161',
        //         'count' => 0
        //     ]);
        // }
        
        // $groupIds = $groups->pluck('id')->toArray();
        
        // Bước 2: Tìm các User thuộc các Group đó (qua DetailUserGroup)
        $userIds = GroupUser::join('users', 'users.group_user', '=', 'group_user.id')
            ->where('group_user.lead_team', 161)
            ->pluck('users.id')
            ->unique()
            ->toArray();
        // Bước 3: Lấy tất cả SaleCare có group_id = 11 hoặc 12 và assign_user thuộc team leader 161
        $saleCares = SaleCare::whereIn('group_id', [11, 12])
            ->whereIn('assign_user', $userIds)
            ->where('old_customer', 0)
            ->whereNull('id_order_new')
            ->whereNull('id_order')
            ->whereDate('created_at', '>=', '2025-11-01')
            ->whereDate('created_at', '<=', '2025-11-30')
            ->limit(1000)
            ->get();

            $i = 1;
            foreach ($saleCares as $item) {
                echo $i . ' - ' . $item->phone . ' - ' . $item->user->real_name . ' _ ' . date_format($item->created_at, 'd-m-Y') . '<br>';
                $i++;
            }   
        // $targetUsers = $groupUser9->users()
        //     ->where('status', 1) // Chỉ lấy user đang active
        //     ->get();
        // if ($targetUsers->isEmpty()) {
        //     return response()->json([
        //         'message' => 'Không tìm thấy User nào trong GroupUser id = 9',
        //         'count' => 0
        //     ]);
        // }
        
        // $targetUserIds = $targetUsers->pluck('id')->toArray();
        // dd($targetUserIds);
       /* $targetUserIds = [
            0 => 171,
            1 => 176,
            2 => 177,
            3 => 181,
            4 => 189,
            5 => 266
        ];
        $totalUsers = count($targetUserIds);
        
        // Bước 5: Chia đều data cho các user trong group_user = 9
        $totalData = $saleCares->count();
        $updated = 0;
        $index = 0;
        
        $listData = [];
        foreach ($saleCares as $saleCare) {
            $isOldCustomer = Helper::isOldCustomerByGroup($saleCare->phone, $saleCare->group_id);
            // dd($saleCare->phone, $saleCare->group_id);
            // dd($isOldCustomer);
            if ($isOldCustomer) {
                continue;
            }
            // Chia đều theo vòng tròn (round-robin)
            $assignUserId = $targetUserIds[$index % $totalUsers];
            
            $saleCare->assign_user = $assignUserId;
            $saleCare->save();
            $listData[] = $saleCare->phone . ' - ' . $assignUserId . '<br>';
            $updated++;
            $index++;
        }
        
        return response()->json([
            'message' => 'Cập nhật thành công',
            'total_sale_care_found' => $totalData,
            'total_users_target' => $totalUsers,
            'updated' => $updated,
            'target_user_ids' => $targetUserIds,
            'list_data' => $listData
        ]);
        */
    }
    
    /**
     * Normalize lead_sale field (có thể là số, mảng, hoặc JSON string)
     */
    private function normalizeLeadSaleField($leadSaleField)
    {
        if (is_null($leadSaleField) || $leadSaleField === '') {
            return [];
        }

        if (is_array($leadSaleField)) {
            return array_map('intval', $leadSaleField);
        }

        if (is_numeric($leadSaleField)) {
            return [(int) $leadSaleField];
        }

        if (is_string($leadSaleField)) {
            $decoded = json_decode($leadSaleField, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_map('intval', $decoded);
            }
        }

        return [];
    }
    public function updateStatusVTPost()
    {
        // Lấy 100 orders có status = 2 và shippingOrder.vendor_ship = 'VTPOST'
        $orders = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
            ->where('orders.status', 2)
            ->where('shipping_order.vendor_ship', 'VTPOST')
            ->select('orders.*')
            ->orderBy('shipping_order.created_at', 'desc')
            ->limit(10)
            ->get();

        $shippingController = new ShippingOrderController();

        foreach ($orders as $order) {
            try {
                // Gọi API chi tiết đơn hàng của Viettel Post
                $orderCode = $order->shippingOrder->order_code;
                $detailData = $shippingController->detailDataVTPost($orderCode);
                dd($detailData);
                if ($detailData && isset($detailData['statusLogs'])) {
                    $statusLogs = $detailData['statusLogs'];
                    
                    // Kiểm tra xem có trạng thái "Giao thành công" không
                    $isDelivered = false;
                    dd($statusLogs);
                    foreach ($statusLogs as $log) {
                        if (isset($log['STATUS_NAME']) && $log['STATUS_NAME'] == 'Giao thành công') {
                            $isDelivered = true;
                            break;
                        }
                    }

                    // Nếu đơn đã thành công thì cập nhật trạng thái và tạo data sale
                    if ($isDelivered) {
                        $order->status = 3;
                        $order->save();

                        // Kiểm tra đơn này đã có data chưa
                        $issetOrder = Helper::checkOrderSaleCare($order->id);

                        // Kiểm tra đơn có sản phẩm paulo không
                        $notHasPaulo = Helper::hasAllPaulo($order->id_product);

                        // Tạo data tác nghiệp sale nếu không có paulo
                        if ($notHasPaulo && $order->saleCare) {
                            $orderTricho = $order->saleCare;
                            $chatId = $groupId = '';
                            $saleCare = $order->saleCare;

                            // Xử lý group và assign user
                            if ($order->saleCare && $saleCare->group) {
                                $group = $saleCare->group;
                                $chatId = $group->tele_cskh_data;
                                $groupId = $group->id;

                                if ($group->is_share_data_cskh && $order->saleCare->old_customer != 1) {
                                    $assgin_user = Helper::getAssignCskhByGroup($group, 'cskh')->id_user;
                                } else {
                                    $assgin_user = $order->saleCare->assign_user;
                                    $user = $order->saleCare->user;

                                    // Tài khoản đã khoá hoặc chặn nhận data => tìm sale khác trong nhóm
                                    if (!$user->is_receive_data || !$user->status) {
                                        $assgin_user = Helper::getAssignSaleByGroup($group, 'cskh')->id_user;
                                    }
                                }
                            } else if (!empty($orderTricho->group_id) && $orderTricho->group_id == 'tricho') {
                                $groupId = 'tricho';
                                $chatId = '-4286962864';
                                $assgin_user = $order->assign_user;
                            } else {
                                $assgin_user = 50;
                                $chatId = '-4558910780';
                            }

                            $typeCSKH = Helper::getTypeCSKH($order);
                            $pageName = $order->saleCare->page_name;
                            $pageId = $order->saleCare->page_id;
                            $pageLink = $order->saleCare->page_link;

                            $sale = new SaleController();
                            $data = [
                                'id_order' => $order->id,
                                'sex' => $order->sex,
                                'name' => $order->name,
                                'phone' => $order->phone,
                                'address' => $order->address,
                                'assgin' => $assgin_user,
                                'page_name' => $pageName,
                                'page_id' => $pageId,
                                'page_link' => $pageLink,
                                'group_id' => $groupId,
                                'chat_id' => $chatId,
                                'type_TN' => $typeCSKH,
                            ];

                            if ($order->saleCare->src_id) {
                                $data['src_id'] = $order->saleCare->src_id;
                            } else if ($order->saleCare->type != 'ladi') {
                                $pageSrc = SrcPage::where('id_page', $order->saleCare->page_id)->first();
                                if ($pageSrc) {
                                    $data['src_id'] = $pageSrc->id;
                                }
                            }

                            if ($issetOrder || $order->id) {
                                $data['old_customer'] = 1;
                            }

                            $request = new \Illuminate\Http\Request();
                            $request->replace($data);
                            $sale->save($request);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Update Status VTPost Error:', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
    }
    public function nhan(){
        $list = User::where('status', 1)
        ->where('is_digital', 1)
        ->select('id', 'name', 'real_name')
        ->get()
        ->toArray();

        foreach ($list as $item) {
            echo $item['id'] . ' - ' . $item['name'] . ' - ' . $item['real_name'] . '<br>';
        }
        // dd($list);
    }
    public function tranh(){
        // Cập nhật tất cả SaleCare có assign_user = 218 thành 242
        $count = SaleCare::where('assign_user', 218)->count();
        
        if ($count == 0) {
            return response()->json([
                'message' => 'Không tìm thấy SaleCare nào có assign_user = 218',
                'count' => 0
            ]);
        }

        $updated = SaleCare::where('assign_user', 218)
            ->update(['assign_user' => 242]);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'total_found' => $count,
            'updated' => $updated
        ]);
    }
    public function nhi(){
        $list = SaleCare::whereDate('created_at', '>=', '2025-10-22')
        ->whereDate('created_at', '<=', '2025-11-11')
        ->where('old_customer', 0)
        ->where('src_id', 48)
        ->get();
        dd($list);
    }
    public function kt(){
        $list = Orders::join('shipping_order', 'shipping_order.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', '>=', '2025-10-01')
            ->whereDate('orders.created_at', '<=', '2025-10-11')
            ->where('shipping_order.vendor_ship', 'GHN')
            ->where('orders.status', 0)
            ->select('orders.*')
            ->get();
        $i = 1;

        $dataExport[] = [
            'STT', 'Ngày nhận', 'Số điện thoại', 'Tên khách'
          ];
        foreach ($list as $data) {
    
          // $tnCan = $data->TN_can;
          // if ($data->listHistory) {
          //   foreach ($data->listHistory as $his) {
          //     $tnCan .= date_format($his->created_at,"d-m-Y ") . ': ' . $his->note . ', ';
          //   }
    
          // }
          // dd($data->user->real_name);
          $dataExport[] = [
            $i,
            date_format($data->created_at,"d-m-Y "),
            $data->phone,
            $data->name,
            // $data->user->real_name ?? '',
          ];
          $i++;
        }
    
        return Excel::download(new UsersExport($dataExport), 'GHN-HUY.xlsx');
    }
    public function huyen(){
        $list = Orders::join('sale_care', 'sale_care.id_order', '=', 'orders.id')
            ->join('src_page', 'src_page.id', '=', 'sale_care.src_id')
            ->whereDate('orders.created_at', '>=', '2025-10-22')
            ->whereDate('orders.created_at', '<=', '2025-10-31')
            ->where('src_page.old_customer', 0)
            // ->limit(100)
            // ->where('id', 99145)
            ->get();
        dd($list);
    }
    public function thuy()
    {
        $listSrc = SrcPage::where('user_digital', 114)->where('type', 'pc')
            ->where('status', 1)
            ->get();
        foreach ($listSrc as $page) {
            $group = $page->group;
            if (!$group) {
                continue;
            }
            $testCtl = new TestController();
            $testCtl->crawlerPancakePage($page, $group);
        }

    }

    public function exportActiveProducts()
    {
        $products = Product::with('category')
          ->where('status', 1)
          ->orderBy('name')
          ->get();
    
        if ($products->isEmpty()) {
          return back()->with('error', 'Không có sản phẩm đang bật để xuất.');
        }
    
        $dataExport = [
          ['STT', 'Tên sản phẩm', 'Tên thuế', '% thuế', 'Danh mục', 'Giá tiền']
        ];
    
        $i = 1;
        foreach ($products as $product) {
          $dataExport[] = [
            $i,
            $product->name,
            $product->tax_name,
            $product->tax,
            $product->category->name ?? '',
            number_format($product->price),
          ];
          $i++;
        }
    
        $fileName = 'san-pham-dang-bat-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new UsersExport($dataExport), $fileName);
    }

    public function getID(){
        $orders = Orders::whereDate('orders.created_at', '>=', '2025-09-01')
        ->whereDate('orders.created_at', '<=', '2025-10-23')
        ->where('orders.status',  '!=', 0)
        // ->where('orders.id', 24013)
        ->get();
        // dd($orders);
        $i = 0;
        foreach ($orders as $order) {
            if (!$order->saleCare) {
                // dd($order);
                $i++;
                echo $i . ' - ' . $order->phone . ' - ' . $order->id . '<br>';
            } 
        }
        echo $i;
        // dd($orders);n_decode($json, true);
        // dd($data);
    }

    public function setID(){
        // Phương pháp 1: Xử lý streaming (khuyến nghị cho file lớn)
       // $this->processJsonStreaming();
        
        // Phương pháp 2: Xử lý toàn bộ file (cần nhiều memory)
         $this->processFullJson();
    }
    
    private function processJsonStreaming() {
        $jsonFile = public_path('json/sale_care.json');
        $handle = fopen($jsonFile, 'r');
        
        $saleCareData = [];
        $inDataArray = false;
        $recordCount = 0;
        $maxRecords = 1000; // Giới hạn để test
        
        while (($line = fgets($handle)) !== false && $recordCount < $maxRecords) {
            $line = trim($line);
            
            // Tìm bắt đầu data array
            if (strpos($line, '"data":') !== false) {
                $inDataArray = true;
                continue;
            }
            
            if ($inDataArray && $line !== ']' && $line !== '[' && $line !== '') {
                // Bỏ dấu phẩy cuối dòng
                if (substr($line, -1) === ',') {
                    $line = substr($line, 0, -1);
                }
                
                // Decode từng record
                $record = json_decode($line, true);
                if ($record) {
                    $saleCareData[] = $record;
                    $recordCount++;
                }
            }
        }
        
        fclose($handle);
        
        dd([
            'method' => 'streaming',
            'records_processed' => count($saleCareData),
            'first_record' => $saleCareData[0] ?? null,
            'memory_usage' => memory_get_usage(true)
        ]);
    }
    
    private function processFullJson() {
        // Tăng memory limit để xử lý file lớn
        ini_set('memory_limit', '1G');
        
        $json = file_get_contents(public_path('json/sale_care.json'));
        
        // Decode JSON thành array
        $data = json_decode($json, true);
        // Kiểm tra lỗi JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('JSON Error: ' . json_last_error_msg());
        }
        
        // Extract data array từ PHPMyAdmin format
        $saleCareData = [];
        foreach ($data as $item) {
            if (isset($item['type']) && $item['type'] === 'table' && isset($item['data'])) {
                $saleCareData = $item['data'];
                break;
            }
        }

        $saleCareBKs = [];

        if (count($saleCareData) > 0) {
            foreach ($saleCareData as $item) {
                $saleCareBKs[$item['id']] = $item;
            }

        }
        // dd($saleCareBKs);
        // dd([
        //     'method' => 'full_decode',
        //     'total_records' => count($saleCareData),
        //     'first_record' => $saleCareData ?? null,
        //     'memory_usage' => memory_get_usage(true)
        // ]);
        $orders = Orders::whereDate('orders.created_at', '>=', '2025-09-01')
        ->whereDate('orders.created_at', '<=', '2025-10-23')
        ->where('orders.status',  '!=', 0)
        // ->where('orders.id', 24013)
        ->get();
        // dd($orders);
        foreach ($orders as $order) {
            if (!$order->saleCare && $order->sale_care && isset($saleCareBKs[$order->sale_care])) {
                $saleBK = $saleCareBKs[$order->sale_care];
                $saleCare = new SaleCare($saleBK);
                $saleCare->save();
                echo $saleCare->id . ' - ' . $order->id . '<br>';
            } 
        }
       
    }
    public function updateName(){
        $list = SaleCare::where('old_customer', 0)
        ->whereDate('created_at', '>=', '2025-12-12')
        ->whereDate('created_at', '<=', '2025-12-30')
        ->whereFullName('Loading')
        ->orderBy('id', 'desc')
        // ->limit(100)
        // ->where('id', 99145)
        ->get();
        // dd($list);
       foreach ($list as $item) {
            $src = $item->getSrcPage;
            $phoneSearch = $item->phone;
            // dd($src->id_page);
            if ($src && ($pIdPan = $src->id_page) && ($token = $src->token)) {
                // dd($pIdPan);
                $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";
                $endpoint = "$endpoint/search?q=$phoneSearch&access_token=$token&cursor_mode=true";
                $response = Http::withHeaders(['access_token' => $token])->get($endpoint);
                // dd($endpoint);
                if ($response->status() == 200) {
                    $content  = json_decode($response->body());
                    
                    // if (isset($content->success) && $content->success == false) {
                    //     dd($content);
                    // }
                    if (isset($content->conversations) && count($content->conversations) > 0) {
                        // dd($content);
                        $data     = $content->conversations;
                        $customer = $data[0]->customers[0];
                        $name = $customer->name;
                        $item->full_name = $name;
                        $item->save();
                        echo $name . ' - ' . $phoneSearch . '<br>';
                    }
                }
            }
       }
    }

    public function getNameFromPancake($phoneSearch)
    {
        // dd($phoneSearch);
        $endpoint = 'https://ads.phanbonmiennam.net/backend/conversations/search';
        $data = [
            "search" => $phoneSearch,
            "limit" => 50,
            "page" => 1,
            "id" => "019b1d1a-2a1b-72cd-2cc4-93893e51719a",
            "type" => "group",
        ];
        
        $response = Http::withHeaders([
            'Cookie' => self::COOKI_USU2,
        ])->post($endpoint, $data);

        $response = $response->json();
        // dd($response);
        if (isset($response['success']) && !$response['success']) {
            echo $response['message'] . '<br>';
            return '';
        }
       
        if ($response['total'] > 0) {
            $data = $response['data'][0]['userName'];
            return $data;
        }
    }
    public function updateNameV2(){
        $list = SaleCare::where('old_customer', 0)
        ->whereDate('created_at', '>=', '2025-12-14')
        ->whereDate('created_at', '<=', '2025-12-30')
        ->whereFullName('Loading')
        ->limit(50)
        // ->where('id', 99145)
        ->get();

        foreach ($list as $item) {
            $phoneSearch = $item->phone;
            $name = $this->getNameFromPancake($phoneSearch);

            if ($name != '') {
                $item->full_name = $name;
                $item->save();
                echo $name . ' - ' . $phoneSearch . '<br>';
            }
        }
        
       
    }

    public function tool()
    {
        $checkAll = isFullAccess(Auth::user()->role);
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        $isMkt = Helper::isMkt(Auth::user());
        if ($isMkt || $isLeadSale || $checkAll) {
            return view('pages.tool.index');
        }

        return redirect()->route('home');
    }

    public function getPhonePc(Request $request, $phoneSearch)
    {
        $srcs = [];
        $pageId = $request->page_id;
        if ($pageId != "") {
            $src = Helper::getPageSrcByPageId($pageId);
            $srcs[] = $src;
        } else {
            $groups = Group::where('status', 1)->get();
            foreach ($groups as $group) {
                $srcs[] = $group->srcs->toArray();
            }
            $srcs = array_merge(...$srcs);
        }

        $phoneSearch = Helper::getCustomPhoneNum($phoneSearch);
        if (Helper::isSeeding($phoneSearch)) {
            return response()->json(['error' => 'true', 'text' => 'Data này đang nằm danh sách đen.']);
        }

        /*$groups = Group::where('status', 1)->get();
        foreach ($groups as $group) {
            $srcs[] = $group->srcs->toArray();
        }

        $srcs = array_merge(...$srcs);*/
        
        foreach ($srcs as $src) {
            if ($src['type'] != 'pc') {
                continue;
            } 
            
            // if ($src['id_page'] != '689087570959486') {
            //     continue;
            // }

            $group = $src->group;
            $srcId = $src['id'];
            $pIdPan = $src['id_page'];
            $token  = $src['token'];
            $namePage = $src['name'];
            $linkPage = $src['link'];
            $endpoint = "https://pancake.vn/api/v1/pages/$pIdPan/conversations";
            // $today    = strtotime(date("Y/m/d H:i"));
            // $before   = strtotime ( '-10 hour' , strtotime( date("Y/m/d H:i"))) ;
            // $before   = date ( 'Y/m/d H:i' , $before );
            // $before   = strtotime($before);

            // $endpoint = "$endpoint?DATE:$before+-+$today&access_token=$token";
            $endpoint = "$endpoint/search?q=$phoneSearch&access_token=$token&cursor_mode=true";
            $response = Http::withHeaders(['access_token' => $token])->get($endpoint);
            // dd($endpoint);
            if ($response->status() == 200) {
                $content  = json_decode($response->body());
                // dd($content);
                if (isset($content->conversations) && count($content->conversations) > 0) {
                    $data     = $content->conversations;
                    // dd($data);
                    foreach ($data as $item) {
                        // dd($item->recent_phone_numbers);
                        if (empty($item->recent_phone_numbers)) {
                            continue;
                        }
                        $recentPhoneNumbers = $item->recent_phone_numbers[0];
                        $mId      = $recentPhoneNumbers->m_id;
                        
                        $phone    = isset($recentPhoneNumbers) ? $recentPhoneNumbers->phone_number : '';
                        $name     = isset($item->customers[0]) ? $item->customers[0]->name : '';
                        $messages = (isset($recentPhoneNumbers) && !empty($recentPhoneNumbers->m_content)) ? $recentPhoneNumbers->m_content : '';
                        $phone = Helper::getCustomPhoneNum($phone);
                        
                        $is_duplicate = $hasOldOrder = $isOldCustomer = $assgin_user = 0;
                        $checkSaleCareOld = Helper::checkOrderSaleCarebyPhoneV5($phone, $mId, $is_duplicate, $hasOldOrder);
                        $typeCSKH = 1;

                        if ($phoneSearch == $phone) {
                            if ($name && $checkSaleCareOld) {
                                $assignSale = Helper::assignSaleFB($hasOldOrder, $group, $phone, $typeCSKH, $isOldCustomer);
                                if (!$assignSale) {
                                    continue;
                                }

                                $assgin_user = $assignSale->id;
                                $is_duplicate = ($is_duplicate) ? 1 : 0;
                                $sale = new SaleController();
                                $data = [
                                'page_link' => $linkPage,
                                'page_name' => $namePage,
                                'sex'       => 0,
                                'old_customer' => $isOldCustomer,
                                'address'   => '',
                                'messages'  => $messages,
                                'name'      => $name,
                                'phone'     => $phone,
                                'page_id'   => $pIdPan,
                                'text'      => 'Page ' . $namePage,
                                'chat_id'   => '',
                                'm_id'      => $mId,
                                'assgin'    => $assgin_user,
                                'is_duplicate' => $is_duplicate,
                                'group_id'  => $group->id,
                                'has_old_order'  => $hasOldOrder,
                                'src_id'  => $srcId,
                                'type_TN' => $typeCSKH, 
                                ];
                                
                                $request = new \Illuminate\Http\Request();
                                $request->replace($data);
                                $sale->save($request);
                                return response()->json(
                                    [
                                        'success'=> true,
                                        'text' => 'Chúc mừng data ' . $name . ' ' . $phone . ' đã được tạo thành công!'
                                    ]);
                            }
                        }
                    }
                }
            }

        }

        return response()->json(['success'=> 'true', 'text' => 'Không tìm thấy dữ liệu phù hợp...']);
    }
}
