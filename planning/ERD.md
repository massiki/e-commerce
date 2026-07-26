# Entity Relationship Diagram (ERD)

## E-Commerce Platform

Dokumen ini berisi Entity Relationship Diagram (ERD) dalam format
Mermaid.

```mermaid
erDiagram

    ROLES ||--o{ USERS : has

    USERS ||--|| CARTS : owns
    USERS ||--|| WISHLISTS : owns
    USERS ||--o{ ADDRESSES : has
    USERS ||--o{ ORDERS : places
    USERS ||--o{ REVIEWS : writes
    USERS ||--o{ NOTIFICATIONS : receives
    USERS ||--o{ ACTIVITY_LOGS : generates
    USERS ||--o{ COUPON_USAGES : uses

    CATEGORIES ||--o{ PRODUCTS : categorizes
    BRANDS ||--o{ PRODUCTS : owns

    PRODUCTS ||--o{ PRODUCT_IMAGES : has
    PRODUCTS ||--o| DISCOUNTS : has
    PRODUCTS ||--o{ REVIEWS : receives
    PRODUCTS ||--o{ CART_ITEMS : added
    PRODUCTS ||--o{ WISHLIST_ITEMS : saved

    CARTS ||--o{ CART_ITEMS : contains
    WISHLISTS ||--o{ WISHLIST_ITEMS : contains

    ORDERS ||--|{ ORDER_ITEMS : contains

    COUPONS ||--o{ COUPON_USAGES : used
    COUPONS ||--o{ ORDERS : applied_to

    ORDER_ITEMS ||--o| REVIEWS : reviewed

    ROLES {
        bigint id PK
        string name
    }

    USERS {
        bigint id PK
        bigint role_id FK
        string name
        string email
        string phone
        string password
    }

    CATEGORIES {
        bigint id PK
        string name
        string slug
        string image
    }

    BRANDS {
        bigint id PK
        string name
        string slug
        string image
    }

    PRODUCTS {
        bigint id PK
        bigint category_id FK
        bigint brand_id FK
        string name
        string slug
        text description
        decimal price
        enum featured
        integer stock
        decimal rating
    }

    PRODUCT_IMAGES {
        bigint id PK
        bigint product_id FK
        string image
    }

    DISCOUNTS {
        bigint id PK
        bigint product_id FK
        decimal value
        datetime start_date
        datetime end_date
    }

    CARTS {
        bigint id PK
        bigint user_id FK
    }

    CART_ITEMS {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        integer quantity
    }

    WISHLISTS {
        bigint id PK
        bigint user_id FK
    }

    WISHLIST_ITEMS {
        bigint id PK
        bigint wishlist_id FK
        bigint product_id FK
    }

    ADDRESSES {
        bigint id PK
        bigint user_id FK
        string recipient_name
        string phone
        string province
        string city
        string district
        string postal_code
        text full_address
    }

    COUPONS {
        bigint id PK
        string code
        string discount_type
        decimal discount_value
        decimal minimum_purchase
        datetime expired_at
    }

    COUPON_USAGES {
        bigint id PK
        bigint coupon_id FK
        bigint user_id FK
    }

    ORDERS {
        bigint id PK
        bigint user_id FK
        string invoice_number
        string recipient_name
        string phone
        string province
        string city
        string district
        string postal_code
        text full_address
        string coupon_code
        decimal coupon_discount
        decimal subtotal
        decimal shipping_cost
        decimal total
        string payment_method
        string snap_token
        enum payment_status
        enum status
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        string product_name
        string image
        decimal price
        integer quantity
        string category_name
        string brand_name
    }

    REVIEWS {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
        bigint order_item_id FK
        integer rating
        string comment
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string title
        boolean is_read
    }

    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        string activity
        timestamp created_at
    }

    SLIDERS {
        bigint id PK
        string title_small
        string title
        string subtitle
        string button_text
        string button_link
        string image
    }
```





## Catatan

- Role → Users = 1:N
- Category → Products = 1:N
- Brand → Products = 1:N
- Product → Product Images = 1:N
- Product → Reviews = 1:N
- User → Orders = 1:N
- User → Addresses = 1:N
- User → Notifications = 1:N
- Order → Order Items = 1:N
- Order Item → Review = 1:0..1
- Address SnapShot Order
- Product, Category, Brand Snapshot Order Item

