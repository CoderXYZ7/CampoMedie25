# Markdown Composer

**Markdown Composer** is a tool for dynamically composing Markdown documents based on modular parts and structured instructions. It reads a single `index.md` file as a blueprint and assembles the final output by merging multiple `.md` files based on defined templates and page types.

---

## 🚀 Features

*   **Dynamic Composition**: Build complex documents from smaller, reusable components.
*   **Template-Based**: Define page structures and required components using simple template files.
*   **Parameter Passing**: Pass parameters from your `index.md` file down to your templates and components.
*   **Recursive Includes**: Include components within other components for more complex document structures.
*   **Image Embedding**: Easily embed images into your documents using the `@include_image` directive.
*   **PDF Export**: Export your final composed document to a PDF file using `pandoc`.
*   **Configurable Directories**: Specify custom directories for your templates and components.
*   **Error Handling**: The script will warn you about missing files or malformed directives without crashing.

---

## 📦 Project Structure

```
project/
├── index.md               # Composition instructions
├── templates/             # Page type definitions
│   └── giorno.template    # Template listing required components
├── components/            # Content pieces organized by type
│   ├── orari/
│   │   ├── 1.md
│   │   ├── 2.md
│   ├── formativo/
│   │   ├── 1.md
│   │   ├── 2.md
│   └── storia/
│       ├── 1.md
│       ├── 2.md
└── output.md              # Final composed document (generated)
```

---

## 🧩 How It Works

1.  `index.md` contains high-level composition instructions.
2.  Each instruction declares a **page type** and its **parameters** (e.g., a day number).
3.  Templates define which components a page type needs.
4.  The composer reads and concatenates the correct files into `output.md`.

---

## ✍️ Syntax for `index.md`

Use the following directive syntax inside `index.md`:

```md
@include [page_type_or_file] [param1] [param2] ...
@include_image [image_path]
```

Examples:

```md
@include giorno 1
@include giorno 2
@include_image assets/logo.png
```

You can add normal Markdown between `@include` lines — it will be passed through untouched.

---

## 🧱 Templates

Templates are plain text files (e.g. `giorno.template`) defining which components are required and how they should be composed.

Example: `templates/giorno.template`

```
orari/{0}.md
formativo/{0}.md
storia/{0}.md
```

*   `{0}` is replaced with the first parameter (`1`, `2`, `3`, etc.).
*   Each line is a Markdown file path to include, relative to the components directory.

---

## ▶️ Example

Given the instruction:

```md
@include giorno 2
```

And the template `giorno.template`:

```
orari/{0}.md
formativo/{0}.md
storia/{0}.md
```

The composer will concatenate:

```
components/orari/2.md
components/formativo/2.md
components/storia/2.md
```

in that order, inserting their contents into `output.md`.

---

## ⚙️ Usage

```bash
python compose.py <index_file> <output_file> [options]
```

**Arguments:**

*   `index_file`: The main index file containing composition instructions.
*   `output_file`: The path to the final composed document.

**Options:**

*   `--templates-dir <dir>`: The directory where template files are stored (default: `templates`).
*   `--components-dir <dir>`: The directory where component files are stored (default: `components`).
*   `--pdf`: Export the output to PDF using `pandoc`.

---

## 🧠 Notes

*   You can create any number of templates (e.g. `giorno`, `evento`, `report`).
*   You can use multiple parameters: `{0}`, `{1}`, etc.
*   Non-`@include` lines in `index.md` are copied verbatim.
*   For PDF export, you must have `pandoc` installed.
