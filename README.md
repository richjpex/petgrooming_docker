# petgrooming_erp
This is a petgrooming website

## Fixes
- [x] F-001: SQLi for edit.php 
- [x] F-002: file upload for profile.php
- [x] F-004: stored XSS (F-004)
- [ ] F-005: edit password
- [ ] F-007: unencrypted HTTP connection

## Endpoints to test
- F-001: https://localhost/admin/view_order.php and then Add Installment Payments
- F-002: https://localhost/admin/profile.php and then Choose File
- F-004: https://localhost/admin/profile.php fix the input validation stuff with regex
- F-005: https://localhost/admin/update_user.php add MFA and require old password