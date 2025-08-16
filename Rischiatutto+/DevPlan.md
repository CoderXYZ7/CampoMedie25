# **Development Plan – Hybrid “Rischiatutto” Game (6×5 Grid)**

## **1. Core Specifications**

* **Grid:** 6 categories × 5 difficulty levels (6×5).
* **Cells:** represent individual questions. Initial state: neutral (gray). After answering: green (correct) or red (incorrect).
* **Questions:** loaded from the "questions.json" file. Each question object includes:

  * `category` (1–6)
  * `level` (1–5)
  * `text` (question content)
  * `type` (normal, double\_risk, stop, solo, team\_play, multiple\_choice)
* **Primary Functions:**

  * Display grid
  * Click on cell → show question + type
  * Two buttons: ✅ Correct / ❌ Incorrect
  * Highlight cell color based on answer
* **Scoring & Turn Management:** handled manually on paper.

---

## **2. Question Data Structure (JSON Example)**

```json
[
  {"category":1, "level":1, "text":"Question 1-1", "type":"normal"},
  {"category":1, "level":2, "text":"Question 1-2", "type":"double_risk"},
  {"category":2, "level":1, "text":"Question 2-1", "type":"stop"}
]
```

---

## **3. Page Layout**

### **Grid**

* 6 columns (categories) × 5 rows (levels)
* Clickable cells
* Cell colors:

  * Gray = unanswered
  * Green = correct
  * Red = incorrect
  * Optional border/indicator for special questions

### **Question Modal**

* Displays question text + type
* Buttons: ✅ Correct / ❌ Incorrect
* After button click → closes modal and updates cell color

---

## **4. User Flow**

1. User clicks a grid cell
2. Modal opens → shows question + type
3. Team answers manually (paper/pen)
4. Moderator clicks Correct or Incorrect
5. Modal closes, cell updates color
6. Repeat until turn ends

---

## **5. Core JS Components**

1. **JSON Loader**

   * Load questions from an external JSON file
   * Populate grid dynamically

2. **Grid Renderer**

   * Render 6×5 clickable grid
   * Assign click handlers for each cell

3. **Question Modal Handler**

   * Display question content and type
   * Handle Correct/Incorrect button clicks

4. **Cell Updater**

   * Update cell color based on answer
   * Apply special styles for special question types

---

## **6. CSS Guidelines**

* Responsive grid, square cells
* Centered modal overlay with dimmed background
* High-contrast colors for projection visibility
* Optional special indicators for question types

---