### 📘 **Project: PHP custom Book Reader System**

#### 1. **Page Types**

* **Basic Pages**

  * Named as sequential numbers: `1.md`, `2.md`, `3.md`, etc.
  * Navigable via a **top scrollable bar** (page numbers).
  * Only basic pages are part of the linear progression.
* **Addendums**

  * Named with non-numeric identifiers: `a1.md`, `appendix_intro.md`, etc.
  * Not listed in the top bar.
  * Can only be accessed via **in-page links** from basic pages or other addendums.
  * Must contain a **"Back"** mechanism to return to the originating basic page.

#### 2. **Content Format**

* Stored in a **custom MD-like format**, possibly allowing:

  * Markdown (for simplicity and content writing).
  * Embedded or raw HTML/CSS/JS when needed.
* Parsing engine should support:

  * Markdown → HTML rendering.
  * Safe HTML embedding.
  * JS/CSS injection if declared inside special tags.

#### 3. **Frontend Structure**

* **Top Bar**: Horizontal scroll bar listing all numeric pages for navigation.
* **Main Content Area**: Renders current page.
* **Link Handling**:

  * Internal links point to either basic or addendum pages.
  * If linking to an addendum, current page must be saved in stack (for back button).

#### 4. **Backend Logic (PHP)**

* Load requested page:

  * Validate input (`1`, `2`, `a1`, etc.).
  * Check file existence in content folder.
  * Parse file (markdown → HTML, include HTML/CSS/JS if present).
* Track "previous" page when opening an addendum.
* Handle “back” requests via history stack (session or client-side JS).

#### 5. **Folder Structure**

```
/book-reader/
  ├── index.php
  ├── parser.php
  ├── /content/
  │    ├── 1.md
  │    ├── 2.md
  │    ├── a1.md
  │    └── ...
  ├── /assets/
  │    ├── style.css
  │    └── script.js
```

#### 6. **Optional Features**

* Highlight active page in top bar.
* Link parser to convert `[Link to A1](a1)` to proper AJAX or load call.
* JS history stack for back button functionality inside addendum pages.

---
