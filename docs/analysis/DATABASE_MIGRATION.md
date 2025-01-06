# Database Migration Plan

## Phase 1: Schema Optimization

### 1.1 Character Set Migration
```sql
-- Migration script template for each table
ALTER TABLE humo_persons CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE humo_families CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- ... (repeat for all tables)
```

### 1.2 Foreign Key Constraints
```sql
-- Core relationships
ALTER TABLE humo_persons
    ADD CONSTRAINT fk_person_tree
    FOREIGN KEY (pers_tree_id) REFERENCES humo_trees(tree_id);

ALTER TABLE humo_families
    ADD CONSTRAINT fk_family_tree
    FOREIGN KEY (fam_tree_id) REFERENCES humo_trees(tree_id);

-- Event relationships
ALTER TABLE humo_events
    ADD CONSTRAINT fk_event_tree
    FOREIGN KEY (event_tree_id) REFERENCES humo_trees(tree_id);
```

### 1.3 Index Optimization
```sql
-- Person search optimization
CREATE INDEX idx_person_names ON humo_persons (pers_firstname, pers_lastname);
CREATE INDEX idx_person_birth ON humo_persons (pers_birth_date, pers_birth_place);
CREATE INDEX idx_person_death ON humo_persons (pers_death_date, pers_death_place);

-- Family search optimization
CREATE INDEX idx_family_dates ON humo_families (fam_marr_date, fam_div_date);
```

## Phase 2: Data Type Updates

### 2.1 Timestamp Standardization
```sql
-- Update timestamp columns
ALTER TABLE humo_persons
    MODIFY pers_new_datetime timestamp DEFAULT CURRENT_TIMESTAMP,
    MODIFY pers_changed_datetime timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Apply to all relevant tables
```

### 2.2 JSON Support
```sql
-- Add JSON columns for flexible data
ALTER TABLE humo_persons ADD COLUMN pers_metadata json;
ALTER TABLE humo_families ADD COLUMN fam_metadata json;
```

### 2.3 Full-Text Search
```sql
-- Add full-text search capabilities
ALTER TABLE humo_persons 
    ADD FULLTEXT INDEX ft_person_search (pers_firstname, pers_lastname, pers_birth_place, pers_death_place);
```

## Phase 3: Performance Optimization

### 3.1 Table Partitioning
```sql
-- Partition large tables by tree_id
ALTER TABLE humo_persons
    PARTITION BY RANGE (pers_tree_id) (
        PARTITION p0 VALUES LESS THAN (100),
        PARTITION p1 VALUES LESS THAN (200),
        PARTITION p2 VALUES LESS THAN MAXVALUE
    );
```

### 3.2 Materialized Views
```sql
-- Create materialized view for common queries
CREATE TABLE mv_person_summary AS
SELECT p.pers_id, p.pers_firstname, p.pers_lastname,
       p.pers_birth_date, p.pers_death_date,
       f.fam_id, f.fam_marr_date
FROM humo_persons p
LEFT JOIN humo_families f ON p.pers_fams LIKE CONCAT('%', f.fam_gedcomnumber, '%');
```

## Implementation Strategy

### 1. Preparation
- Create backup of current database
- Set up test environment
- Prepare rollback scripts

### 2. Execution Order
1. Character set migration
2. Add new indexes
3. Add foreign key constraints
4. Update data types
5. Add JSON support
6. Implement full-text search
7. Configure partitioning
8. Create materialized views

### 3. Validation Steps
- Verify data integrity
- Test application functionality
- Measure performance impact
- Validate GEDCOM compatibility

### 4. Rollback Procedures
```sql
-- Template for each change
BEGIN TRANSACTION;
    -- Revert changes
    -- Restore original state
COMMIT;
```

## Monitoring & Maintenance

### Performance Metrics
- Query execution times
- Index usage statistics
- Storage requirements
- Cache hit rates

### Maintenance Tasks
```sql
-- Regular optimization
OPTIMIZE TABLE humo_persons, humo_families;
ANALYZE TABLE humo_persons, humo_families;

-- Update statistics
UPDATE mysql.innodb_table_stats SET n_rows = NULL WHERE database_name = 'humogen';
```

## Security Considerations

### Data Protection
```sql
-- Encrypt sensitive columns
ALTER TABLE humo_users
    MODIFY user_password_salted varbinary(255);

-- Add column encryption
ALTER TABLE humo_persons
    ADD COLUMN pers_private_notes text ENCRYPTED;
```

### Access Control
```sql
-- Create specific user roles
CREATE ROLE 'humo_reader', 'humo_editor', 'humo_admin';

-- Grant appropriate permissions
GRANT SELECT ON humogen.* TO 'humo_reader';
GRANT SELECT, INSERT, UPDATE ON humogen.* TO 'humo_editor';
``` 