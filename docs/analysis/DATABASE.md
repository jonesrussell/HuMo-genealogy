# HuMo-genealogy Database Analysis

## Overview

The database uses a comprehensive schema designed for genealogical data management. It follows a structured approach with clear relationships between core entities like persons, families, and events.

## Core Tables

### Persons (`humo_persons`)
Primary table for individual records.
```sql
Primary: pers_id (int)
Key Fields:
- pers_gedcomnumber (varchar 25) - Unique identifier
- pers_tree_id (mediumint) - Family tree reference
- pers_famc (varchar 50) - Family where person is child
- pers_fams (varchar 150) - Families where person is spouse
```

### Families (`humo_families`)
Represents family units and relationships.
```sql
Primary: fam_id (int)
Key Fields:
- fam_gedcomnumber (varchar 25) - Unique identifier
- fam_tree_id (mediumint) - Family tree reference
- fam_man (varchar 25) - Reference to husband
- fam_woman (varchar 25) - Reference to wife
```

### Events (`humo_events`)
Records life events and occurrences.
```sql
Primary: event_id (int)
Key Fields:
- event_tree_id (smallint)
- event_connect_kind (varchar 25)
- event_connect_id (varchar 25)
```

## Supporting Tables

### Addresses (`humo_addresses`)
Physical location information.
```sql
Primary: address_id (int)
Key Fields:
- address_tree_id (smallint)
- address_connect_id (varchar 25)
```

### Sources (`humo_sources`)
Documentation and references.
```sql
Primary: source_id (int)
Key Fields:
- source_tree_id (smallint)
- source_gedcomnr (varchar 25)
```

### Repositories (`humo_repositories`)
Source repositories information.
```sql
Primary: repo_id (int)
Key Fields:
- repo_tree_id (smallint)
- repo_gedcomnr (varchar 25)
```

## System Tables

### Trees (`humo_trees`)
Family tree management.
```sql
Primary: tree_id (smallint)
Key Fields:
- tree_prefix (varchar 10)
- tree_privacy (varchar 100)
```

### Users (`humo_users`)
User management and authentication.
```sql
Primary: user_id (smallint)
Key Fields:
- user_name (varchar 25)
- user_group_id (smallint)
```

### Groups (`humo_groups`)
Access control and permissions.
```sql
Primary: group_id (smallint)
Key Fields:
- group_privacy (varchar 1)
- group_admin (varchar 1)
```

## Relationships

### Core Relationships
1. Person → Family (Child)
   - `humo_persons.pers_famc` → `humo_families.fam_gedcomnumber`

2. Person → Family (Spouse)
   - `humo_persons.pers_fams` → `humo_families.fam_gedcomnumber`

3. Family → Person (Parents)
   - `humo_families.fam_man` → `humo_persons.pers_gedcomnumber`
   - `humo_families.fam_woman` → `humo_persons.pers_gedcomnumber`

### Supporting Relationships
1. Events → Person/Family
   - `humo_events.event_connect_id` → Various tables

2. Addresses → Person/Family
   - `humo_addresses.address_connect_id` → Various tables

3. Sources → Various Entities
   - Connected through intermediate tables

## Data Types

### Common Fields
- IDs: Integer types (AUTO_INCREMENT)
- References: varchar(25) for GEDCOM numbers
- Names/Places: varchar(60-120)
- Dates: varchar(35) for genealogical dates
- Text: text for large content
- Timestamps: datetime for tracking changes

### Special Fields
- Privacy/Status: varchar(1) for flags
- Tree IDs: smallint/mediumint
- Order fields: mediumint for sorting

## Observations

### Strengths
1. **Flexible Structure**
   - Supports multiple family trees
   - Handles complex relationships
   - Comprehensive event tracking

2. **Data Integrity**
   - Proper primary keys
   - Consistent reference fields
   - Change tracking

3. **Privacy Controls**
   - User/group system
   - Tree-level privacy
   - Record-level visibility

### Areas for Improvement
1. **Schema Optimization**
   - Some redundant fields
   - Inconsistent field lengths
   - Mixed character sets

2. **Constraints**
   - Limited foreign key constraints
   - Missing index opportunities
   - Inconsistent null handling

3. **Modern Features**
   - No JSON support
   - Limited full-text capabilities
   - Basic timestamp handling

## Recommendations

### Short Term
1. **Indexing**
   - Add missing indexes
   - Optimize existing indexes
   - Review compound indexes

2. **Constraints**
   - Add foreign key constraints
   - Standardize null handling
   - Add check constraints

3. **Character Sets**
   - Standardize on utf8mb4
   - Review collations
   - Update field definitions

### Long Term
1. **Schema Evolution**
   - Normalize redundant data
   - Add metadata tables
   - Implement soft deletes

2. **Performance**
   - Partition large tables
   - Implement caching
   - Add materialized views

3. **Features**
   - Add full-text search
   - Implement JSON columns
   - Add spatial support

## Migration Considerations

### Data Preservation
- Preserve GEDCOM compatibility
- Maintain existing references
- Handle legacy data formats

### Performance
- Plan for large datasets
- Consider table partitioning
- Implement batch processing

### Compatibility
- Maintain backward compatibility
- Support legacy queries
- Plan upgrade paths 