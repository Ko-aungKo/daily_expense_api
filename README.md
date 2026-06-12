# Expense Tracker API — Frontend Integration Guide

This repository hosts a production-ready GraphQL API for a Personal Finance/Expense Tracker application. This guide contains everything the frontend team needs to know to authenticate, query, and mutate data.

---

## 1. Connection Details

* **GraphQL Endpoint**: `POST http://localhost/graphql`
* **Request Headers**:
  ```http
  Content-Type: application/json
  Accept: application/json
  ```

---

## 2. Authentication Flow

The API uses **Laravel Sanctum** token-based authentication. All queries and mutations (except `login` and `register`) require a Bearer token.

### Setting the Authorization Header
Once you obtain the token from login or registration, attach it to all future requests:
```http
Authorization: Bearer <your_access_token>
```

---

## 3. GraphQL API Reference & Code Snippets

### 🔑 Authentication

#### 1. Register a User
```graphql
mutation Register($name: String!, $email: String!, $password: String!) {
  register(name: $name, email: $email, password: $password) {
    user {
      id
      name
      email
    }
    token
  }
}
```
* **Variables:**
  ```json
  {
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "securepassword123"
  }
  ```

#### 2. Log In
```graphql
mutation Login($email: String!, $password: String!) {
  login(email: $email, password: $password) {
    user {
      id
      name
      email
    }
    token
  }
}
```
* **Variables:**
  ```json
  {
    "email": "john.doe@example.com",
    "password": "securepassword123"
  }
  ```

#### 3. Log Out *(Requires Bearer Token)*
```graphql
mutation Logout {
  logout {
    success
    message
  }
}
```

#### 4. Fetch Current User Profile *(Requires Bearer Token)*
```graphql
query Me {
  me {
    id
    name
    email
    created_at
  }
}
```

---

### 📊 Dashboard Metrics *(Requires Bearer Token)*
Returns total expenses (today, this month, this year), top categories by spent amount, and recent expenses.

```graphql
query GetDashboard {
  dashboard {
    totalSpentToday
    totalSpentThisMonth
    totalSpentThisYear
    expenseCount
    topCategories {
      category {
        id
        name
        color
        icon
      }
      totalAmount
      count
    }
    recentExpenses {
      id
      amount
      description
      expense_date
      category {
        name
      }
      paymentMethod {
        name
      }
    }
  }
}
```

---

### 📁 Categories *(Requires Bearer Token)*

#### 1. Get Categories
```graphql
query GetCategories {
  categories {
    id
    name
    icon
    color
    is_default
  }
}
```

#### 2. Create Category
```graphql
mutation CreateCategory($name: String!, $icon: String, $color: String) {
  createCategory(name: $name, icon: $icon, color: $color) {
    id
    name
    icon
    color
    is_default
  }
}
```
* **Variables:**
  ```json
  {
    "name": "Groceries",
    "icon": "shopping-cart",
    "color": "#4CAF50"
  }
  ```

#### 3. Update Category
```graphql
mutation UpdateCategory($id: ID!, $name: String!, $icon: String, $color: String) {
  updateCategory(id: $id, name: $name, icon: $icon, color: $color) {
    id
    name
    icon
    color
  }
}
```

#### 4. Delete Category
```graphql
mutation DeleteCategory($id: ID!) {
  deleteCategory(id: $id) {
    success
    message
  }
}
```

---

### 💳 Payment Methods *(Requires Bearer Token)*

#### 1. Get Payment Methods
```graphql
query GetPaymentMethods {
  paymentMethods {
    id
    name
  }
}
```

#### 2. Create Payment Method
```graphql
mutation CreatePaymentMethod($name: String!) {
  createPaymentMethod(name: $name) {
    id
    name
  }
}
```

#### 3. Update Payment Method
```graphql
mutation UpdatePaymentMethod($id: ID!, $name: String!) {
  updatePaymentMethod(id: $id, name: $name) {
    id
    name
  }
}
```

#### 4. Delete Payment Method
```graphql
mutation DeletePaymentMethod($id: ID!) {
  deletePaymentMethod(id: $id) {
    success
    message
  }
}
```

---

### 💸 Expenses *(Requires Bearer Token)*

#### 1. Get Paginated & Filtered Expenses
This query uses Relay-style Connection pagination (`edges` containing a list of `node` items).

```graphql
query GetExpenses(
  $startDate: Date
  $endDate: Date
  $categoryId: ID
  $paymentMethodId: ID
  $minAmount: Float
  $maxAmount: Float
  $search: String
  $sort: String
) {
  expenses(
    startDate: $startDate
    endDate: $endDate
    categoryId: $categoryId
    paymentMethodId: $paymentMethodId
    minAmount: $minAmount
    maxAmount: $maxAmount
    search: $search
    sort: $sort
  ) {
    edges {
      node {
        id
        amount
        description
        expense_date
        category {
          id
          name
        }
        paymentMethod {
          id
          name
        }
      }
    }
    pageInfo {
      hasNextPage
      endCursor
    }
  }
}
```
* **Filtering & Sorting Options:**
  * `sort`: Accepts format `"column_name,direction"` (e.g. `"expense_date,desc"` or `"amount,asc"`).
  * `search`: Performs a search on the `description` field.

#### 2. Get Single Expense Details
```graphql
query GetExpense($id: ID!) {
  expense(id: $id) {
    id
    amount
    description
    expense_date
    category {
      id
      name
    }
    paymentMethod {
      id
      name
    }
  }
}
```

#### 3. Create Expense
```graphql
mutation CreateExpense(
  $amount: Float!
  $categoryId: ID!
  $paymentMethodId: ID!
  $expenseDate: Date!
  $description: String
) {
  createExpense(
    amount: $amount
    category_id: $categoryId
    payment_method_id: $paymentMethodId
    expense_date: $expenseDate
    description: $description
  ) {
    id
    amount
    expense_date
    description
  }
}
```

#### 4. Update Expense
```graphql
mutation UpdateExpense(
  $id: ID!
  $amount: Float!
  $categoryId: ID!
  $paymentMethodId: ID!
  $expenseDate: Date!
  $description: String
) {
  updateExpense(
    id: $id
    amount: $amount
    category_id: $categoryId
    payment_method_id: $paymentMethodId
    expense_date: $expenseDate
    description: $description
  ) {
    id
    amount
    expense_date
    description
  }
}
```

#### 5. Delete Expense *(Soft Deletes)*
```graphql
mutation DeleteExpense($id: ID!) {
  deleteExpense(id: $id) {
    success
    message
  }
}
```

---

## 4. Error Formats

### 1. Validation Errors (HTTP 200 with GraphQL Errors array)
When validation fails (e.g. a required field is missing or an email is already taken), the API returns a standard GraphQL error response with a validation payload:

```json
{
  "errors": [
    {
      "message": "The given data was invalid.",
      "extensions": {
        "validation": {
          "email": [
            "The email has already been taken."
          ]
        }
      }
    }
  ]
}
```

### 2. Unauthenticated Error (HTTP 200 with GraphQL Errors array)
If a query or mutation requires authentication but the Bearer token is missing, expired, or invalid:

```json
{
  "errors": [
    {
      "message": "Unauthenticated.",
      "extensions": {
        "guards": [
          "sanctum"
        ]
      }
    }
  ]
}
```
