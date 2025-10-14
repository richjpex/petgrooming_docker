# petgrooming_erp
This is a petgrooming website

## Link to pentest report
https://docs.google.com/document/d/1NAnb-iX-YRf3ixIHzveerAvNZNx9gZSKx0aVy8lelsc/edit?usp=sharing

## Fixes
- [x] F-001: SQLi for edit.php 
- [x] F-002: file upload for profile.php
- [x] F-003: hardcoded credentials in config.php
- [x] F-004: stored XSS (F-004)
- [ ] F-005: edit password
- [ ] F-007: unencrypted HTTP connection

## Endpoints to test
- F-001: https://localhost/admin/view_order.php and then Add Installment Payments
- F-002: https://localhost/admin/profile.php and then Choose File
- F-004: https://localhost/admin/profile.php fix the input validation stuff with regex
- F-005: https://localhost/admin/update_user.php add MFA and require old password
