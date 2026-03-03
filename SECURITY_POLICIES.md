# TriadCo Hotel Management System - Security Policies

## Table of Contents
1. [Password Policy](#1-password-policy)
2. [Login Attempt Policy](#2-login-attempt-policy)
3. [Data Handling Policy](#3-data-handling-policy)
4. [Input Validation and Size Limits](#4-input-validation-and-size-limits)
5. [Access Control Policy](#5-access-control-policy)
6. [Logging and Monitoring Policy](#6-logging-and-monitoring-policy)
7. [Backup and Recovery Policy](#7-backup-and-recovery-policy)
8. [Incident Response Plan](#8-incident-response-plan)

---

## 1. Password Policy

### Password Requirements
| Requirement | Standard |
|-------------|----------|
| Minimum Length | 12 characters |
| Character Types | Alphanumeric (letters A-Z, a-z and numbers 0-9) |
| Password Hashing | bcrypt with 12 rounds |
| Password Storage | Never stored in plain text |

### Password Best Practices
- Passwords must be unique and not reused across accounts
- Default admin passwords must be changed immediately after first login
- Passwords should not contain personal information
- Password confirmation required for all password changes

### Implementation Details
- **Hashing Algorithm**: bcrypt (BCRYPT_ROUNDS=12)
- **Validation**: Server-side regex validation `/^[a-zA-Z0-9]+$/`
- **Confirmation**: Password confirmation field required on all registration/update forms

---

## 2. Login Attempt Policy

### Account Lockout Rules
| Setting | Value |
|---------|-------|
| Maximum Failed Attempts | 5 |
| Lockout Duration | 15 minutes |
| Lockout Reset | Automatic after duration expires |

### Security Features
- **Failed Login Notification**: Users are notified of failed login attempts upon successful login
- **Login Logging**: All login attempts (successful and failed) are recorded
- **IP Tracking**: IP addresses are logged for all authentication attempts
- **User Agent Tracking**: Browser/device information recorded for forensic analysis
- **CAPTCHA Protection**: Math-based CAPTCHA required for all login attempts

### Inactive Account Handling
- Inactive accounts cannot authenticate
- Login attempts on inactive accounts are logged as security events
- Users receive clear notification about account status

---

## 3. Data Handling Policy

### Encryption Standards
| Data Type | Encryption Method |
|-----------|-------------------|
| Passwords | bcrypt hash (12 rounds) |
| Sessions | AES-256-CBC (SESSION_ENCRYPT=true) |
| Cookies | Encrypted using APP_KEY |
| Database Connections | SSL/TLS supported |

### Sensitive Data Protection
| Data Field | Protection Method |
|------------|-------------------|
| Phone Numbers | Masked display (****-****-1234) |
| Email Addresses | Masked display (j***@example.com) |
| SSS Numbers | Masked display (***-**-1234) |
| Addresses | Masked display |

### Data Unmasking Requirements
1. User must authenticate their identity
2. Password verification required
3. Verification token valid for 5 minutes only
4. All unmask actions are logged with:
   - User ID
   - Record type and ID
   - Field unmasked
   - IP address
   - Timestamp

### Authorized Access Rules
- Only authenticated users can access protected data
- Role-based restrictions apply to all sensitive operations
- Admin-only access for employee management
- Credential verification required for sensitive actions

---

## 4. Input Validation and Size Limits

### Request Size Limits
| Parameter | Limit |
|-----------|-------|
| Total Request Size | 5 MB |
| Maximum Array Items | 100 items |
| File Upload Size | 10 MB |

### Field Size Limits by Type
| Field Type | Maximum Characters |
|------------|-------------------|
| Default String | 1,000 |
| Text/Description | 10,000 |
| Email | 255 |
| Name/Title | 255 |
| Phone Number | 20 |
| IP Address | 45 |
| Password | 128 |
| URL | 2,048 |
| JSON Data | 65,535 |

### Server-Side Validation
All input fields are validated at the server level with:
- **Type Validation**: String, integer, email, date, etc.
- **Size Constraints**: Maximum length enforcement
- **Format Validation**: Regex patterns for specific fields
- **Unique Constraints**: Database-level uniqueness checks

### Security Pattern Detection
The system automatically rejects inputs containing:
- SQL injection patterns (UNION SELECT, DROP TABLE, etc.)
- Command injection patterns (shell commands)
- Path traversal attempts (../../)
- Null byte injection
- Excessively repeated characters (potential DoS)

### Implementation
- **Middleware**: `LimitInputSize` middleware validates all requests
- **Validation Layer**: Laravel validation rules with max constraints
- **PHP Settings**: `post_max_size` and `upload_max_filesize` configured

### Recommended PHP.ini Settings
```ini
post_max_size = 8M
upload_max_filesize = 10M
max_input_vars = 1000
max_input_time = 60
memory_limit = 128M
```

---

## 5. Access Control Policy

### Role-Based Access Control (RBAC)

| Role | Access Level |
|------|--------------|
| **Admin** | Full system access, employee management, security settings |
| **Inventory Manager** | Inventory, suppliers, stock management |
| **Room Manager** | Room management, room reports |

### Protected Resources

#### Admin-Only Resources
- Dashboard (`/dashboard`)
- Employee Management (`/employees/*`)
- Security Dashboard (`/security/*`)
- Incident Management (`/incidents/*`)
- System Logs (`/system-logs`)
- User Management

#### Inventory Manager Resources
- Inventory (`/inventory/*`)
- Suppliers (`/suppliers/*`)
- Stock In (`/stock_in/*`)
- Item Categories

#### Room Manager Resources
- Rooms (`/rooms/*`)
- Room Types
- Stock Out (for room assignments)

### Configuration Access
- All system configuration changes require admin authentication
- Configuration access attempts are logged
- Unauthorized access attempts trigger security events

---

## 6. Logging and Monitoring Policy

### Logged Events

#### Authentication Events
| Event | Data Logged |
|-------|-------------|
| Successful Login | User, IP, User Agent, Timestamp |
| Failed Login | Username attempted, IP, Reason, Timestamp |
| Account Lockout | User, Duration, Attempts count |
| Logout | User, Session duration |

#### System Events
| Event | Data Logged |
|-------|-------------|
| User Creation | Admin who created, New user details |
| User Modification | Admin who modified, Changes made |
| User Deletion | Admin who deleted, Deleted user details |
| Data Unmask | User, Record, Field, Timestamp |

#### Security Events
| Event | Data Logged |
|-------|-------------|
| Credential Verification | User, Action, Success/Failure |
| Failed Authorizations | User, Resource attempted |
| IP Blocklist Actions | Admin, IP, Reason |

### Log Retention
- System logs retained for minimum 90 days
- Security logs retained for minimum 1 year
- Login logs retained for minimum 6 months

### Log Review Process
- Security logs reviewed weekly by administrator
- Failed login patterns analyzed for potential attacks
- Anomalous activities investigated promptly

---

## 7. Backup and Recovery Policy

### Backup Schedule
| Backup Type | Frequency | Retention |
|-------------|-----------|-----------|
| Full Database Backup | Weekly (Sunday 2:00 AM) | 4 weeks |
| Incremental Backup | Daily (2:00 AM) | 7 days |
| Configuration Backup | After any change | 30 days |

### Backup Storage
- Primary backups stored on separate physical storage
- Secondary copies maintained off-site
- Backups encrypted using AES-256

### Recovery Procedures
1. **Immediate Response**: Assess damage scope
2. **Data Recovery**: Restore from most recent backup
3. **Validation**: Verify data integrity
4. **Testing**: Test system functionality
5. **Documentation**: Record incident and recovery steps

### Recovery Time Objectives (RTO)
- Critical systems: 4 hours
- Non-critical systems: 24 hours

### Recovery Point Objectives (RPO)
- Maximum acceptable data loss: 24 hours

---

## 8. Incident Response Plan

### Phase 1: Detection
**Indicators of Compromise:**
- Multiple failed login attempts from same IP
- Unusual access patterns outside business hours
- Unauthorized access attempts to restricted resources
- Data unmask requests at unusual volumes
- System error spike

**Detection Methods:**
- Automated login failure monitoring
- System log analysis
- User behavior analytics
- IP blocklist monitoring

### Phase 2: Reporting
**Internal Reporting:**
1. Security incident logged in System Logs
2. Admin notification via dashboard alert
3. Email notification to system administrator

**Reporting Information:**
- Date and time of detection
- Type of incident
- Affected systems/users
- Initial assessment of impact

### Phase 3: Containment
**Immediate Actions:**
- Disable compromised accounts
- Block suspicious IP addresses
- Isolate affected systems if necessary
- Preserve evidence (logs, screenshots)

**Short-term Containment:**
- Reset passwords for affected accounts
- Revoke active sessions
- Increase monitoring on related systems

### Phase 4: Eradication
- Identify root cause of incident
- Remove malicious access or code
- Patch vulnerabilities exploited
- Update security configurations

### Phase 5: Recovery
- Restore systems from clean backups
- Verify system integrity
- Gradual restoration of services
- Enhanced monitoring during recovery

### Phase 6: Post-Incident Review
- Document complete incident timeline
- Analyze what went wrong
- Identify improvements needed
- Update security policies as necessary
- Train staff on lessons learned

---

## Document Control

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | February 2026 | System Admin | Initial version |

**Last Updated**: February 28, 2026

**Review Frequency**: Quarterly or after any security incident

---

## Contact Information

**Security Administrator**: admin@triadco.com

**For security incidents, contact immediately.**
