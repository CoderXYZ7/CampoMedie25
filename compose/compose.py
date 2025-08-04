import sys
import os
import argparse
import base64
import subprocess
import tempfile
import shutil
from PIL import Image

def read_file(path):
    """Reads the content of a file and returns it as a string."""
    try:
        with open(path, 'r') as f:
            return f.read()
    except FileNotFoundError:
        print(f"Error: File not found at '{path}'", file=sys.stderr)
        return None

def write_file(path, content):
    """Writes content to a file."""
    with open(path, 'w') as f:
        f.write(content)

def get_image_embedding(path, temp_dir):
    """Converts an image to JPG, saves it to a temporary directory, and returns a Markdown image link."""
    try:
        # Convert image to JPG to avoid issues with pandoc
        image = Image.open(path)
        jpg_path = os.path.join(temp_dir, os.path.splitext(os.path.basename(path))[0] + '.jpg')
        image.convert('RGB').save(jpg_path)
        return f"![Image]({jpg_path})"
    except FileNotFoundError:
        print(f"Error: Image not found at '{path}'", file=sys.stderr)
        return None
    except Exception as e:
        print(f"Error converting image: {e}", file=sys.stderr)
        return None

def compose(file_path, templates_dir, components_dir, temp_dir, params=[]):
    """Recursively composes a Markdown document by processing @include and @include_image directives."""
    content = read_file(file_path)
    if content is None:
        return ""

    # First pass to handle includes and image embeddings
    processed_content = ""
    for line_num, line in enumerate(content.splitlines(), 1):
        if line.startswith('@include_image'):
            parts = line.split()
            if len(parts) < 2:
                print(f"Warning: Malformed @include_image on line {line_num} in '{file_path}'. Skipping.", file=sys.stderr)
                continue
            image_path = os.path.join(components_dir, parts[1].format(*params))
            image_embedding = get_image_embedding(image_path, temp_dir)
            if image_embedding:
                processed_content += image_embedding + '\n'

        elif line.startswith('@include'):
            parts = line.split()
            if len(parts) < 2:
                print(f"Warning: Malformed @include on line {line_num} in '{file_path}'. Skipping.", file=sys.stderr)
                continue

            include_target = parts[1]
            new_params = parts[2:] if len(parts) > 2 else params

            template_path = os.path.join(templates_dir, f'{include_target}.template')
            if os.path.exists(template_path):
                template_content = read_file(template_path)
                if template_content is None:
                    print(f"Warning: Template for '{include_target}' not found. Skipping include on line {line_num} in '{file_path}'.", file=sys.stderr)
                    continue

                for component_path_template in template_content.splitlines():
                    try:
                        component_path = os.path.join(components_dir, component_path_template.format(*new_params))
                        component_content = compose(component_path, templates_dir, components_dir, temp_dir, new_params)
                        if component_content is not None:
                            processed_content += component_content + '\n'
                    except IndexError:
                        print(f"Warning: Not enough parameters for template placeholder in '{template_path}' for include on line {line_num} in '{file_path}'. Skipping component.", file=sys.stderr)
            else:
                component_path = os.path.join(components_dir, include_target.format(*new_params))
                component_content = compose(component_path, templates_dir, components_dir, temp_dir, new_params)
                if component_content is not None:
                    processed_content += component_content + '\n'
        else:
            processed_content += line + '\n'

    # Second pass to format parameters
    try:
        return processed_content.format(*params)
    except IndexError:
        # Not all placeholders were filled, which is fine if the content is not a component
        return processed_content

def is_pandoc_installed():
    """Checks if pandoc is installed on the system."""
    try:
        subprocess.run(['pandoc', '--version'], capture_output=True, check=True)
        return True
    except (subprocess.CalledProcessError, FileNotFoundError):
        return False

def main():
    """Parses command-line arguments and runs the composer."""
    parser = argparse.ArgumentParser(description='Dynamically compose Markdown documents.')
    parser.add_argument('index_file', help='The main index file containing composition instructions.')
    parser.add_argument('output_file', help='The path to the final composed document.')
    parser.add_argument('--templates-dir', default='templates', help='The directory where template files are stored.')
    parser.add_argument('--components-dir', default='components', help='The directory where component files are stored.')
    parser.add_argument('--pdf', action='store_true', help='Export the output to PDF using pandoc.')

    args = parser.parse_args()

    temp_dir = tempfile.mkdtemp()

    try:
        final_content = compose(args.index_file, args.templates_dir, args.components_dir, temp_dir).strip()
        
        if args.pdf:
            if not is_pandoc_installed():
                print("Error: pandoc is not installed. Please install it to use the --pdf option.", file=sys.stderr)
                sys.exit(1)
            
            output_filename = os.path.splitext(args.output_file)[0]
            md_file_path = f'{output_filename}.md'
            pdf_file_path = f'{output_filename}.pdf'
            
            write_file(md_file_path, final_content)
            
            try:
                subprocess.run(['pandoc', md_file_path, '-o', pdf_file_path, '--resource-path', temp_dir], check=True)
                print(f"Successfully exported to {pdf_file_path}")
            except subprocess.CalledProcessError as e:
                print(f"Error exporting to PDF: {e}", file=sys.stderr)
                sys.exit(1)
        else:
            write_file(args.output_file, final_content)
    finally:
        shutil.rmtree(temp_dir)


if __name__ == '__main__':
    main()
