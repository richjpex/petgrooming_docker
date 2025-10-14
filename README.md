# petgrooming_erp
This is a Pet Grooming Management Website for Security Engineering Internship at RT&Co Cybersecurity

## How to run web app
1. Make sure you run `chmod +x run.sh` first
2. Run the `./run.sh` script

## Link to pentest report
https://docs.google.com/document/d/1NAnb-iX-YRf3ixIHzveerAvNZNx9gZSKx0aVy8lelsc/edit?usp=sharing

## Fixes
- [x] F-001: SQLi for edit.php 
- [x] F-002: file upload for profile.php
- [x] F-003: hardcoded credentials in config.php
- [x] F-004: stored XSS (F-004)
- [x] F-005: edit password
- [x] F-007: unencrypted HTTP connection

## Endpoints to test
- F-001: https://localhost/admin/view_order.php and then Add Installment Payments, run sqlmap
- F-002: https://localhost/admin/profile.php and then Choose File, upload random files and see if it updates
- F-003: try looking for hardcoded credentials or secrets in the source code (do not include docker-compose.yml)
- F-004: https://localhost/admin/profile.php fix the input validation stuff and have alerts
- F-005: https://localhost/admin/update_user.php require old password to change
- F-007: check if traffic is encrypted on wireshark
