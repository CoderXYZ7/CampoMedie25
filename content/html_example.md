<!-- In this page we'll use direct HTML -->

<h2>External Links</h2>
<p>You can create links that open in a new tab using the <code>target="_blank"</code> attribute:</p>
<a href="https://www.google.com" target="_blank">Open Google in a new tab</a>

<hr>

<h2>Tables</h2>
<p>Here is an example of a simple HTML table:</p>
<table>
  <thead>
    <tr>
      <th>Header 1</th>
      <th>Header 2</th>
      <th>Header 3</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Row 1, Cell 1</td>
      <td>Row 1, Cell 2</td>
      <td>Row 1, Cell 3</td>
    </tr>
    <tr>
      <td>Row 2, Cell 1</td>
      <td>Row 2, Cell 2</td>
      <td>Row 2, Cell 3</td>
    </tr>
  </tbody>
</table>

<hr>

<h2>Images</h2>
<p>You can embed images and control their size:</p>
<img src="assets/dummy_image.png" alt="Dummy Image" width="150">

<hr>

<h2>Custom Styling</h2>
<p>You can apply inline CSS for custom styling:</p>
<p style="color:blue; font-size:20px; text-shadow: 2px 2px 4px #aaa;">This paragraph has custom styling.</p>

<hr>

<h2>JavaScript</h2>
<p>You can embed JavaScript to add interactive elements. Click the button below:</p>
<button onclick="showAlert()">Click me</button>
<script>
function showAlert() {
  alert("Hello from embedded JavaScript!");
}
</script>
