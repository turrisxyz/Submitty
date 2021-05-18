from pathlib import Path
from unittest import TestCase, TextTestResult, TextTestRunner, main
import subprocess
from tempfile import NamedTemporaryFile, gettempdir
from timeit import default_timer as timer

from submitty_utils import submitty_schema_validator


THIS_DIR = Path(__file__).resolve().parent
ROOT_DIR = THIS_DIR / '..'
GRADING_DIR = ROOT_DIR / 'grading'
AUTOGRADING_EXAMPLES = ROOT_DIR / 'more_autograding_examples'
TUTORIAL_EXAMPLES = ROOT_DIR / '..' / 'Tutorial' / 'examples'
CONFIGURE = Path(gettempdir(), 'configure.out')


def build_configure():
    cpp_files = [
        'main_configure.cpp',
        'load_config_json.cpp',
        'execute.cpp',
        'TestCase.cpp',
        'error_message.cpp',
        'window_utils.cpp',
        'dispatch.cpp',
        'change.cpp',
        'difference.cpp',
        'tokenSearch.cpp',
        'tokens.cpp',
        'clean.cpp',
        'execute_limits.cpp',
        'seccomp_functions.cpp',
        'empty_custom_function.cpp'
    ]

    grading_dir = ROOT_DIR / 'grading'

    args = [str(grading_dir / x) for x in cpp_files]

    print("Generating configure.out")
    subprocess.check_call([
        "g++",
        *args,
        '-pthread',
        '-g',
        '-std=c++11',
        '-lseccomp',
        '-o',
        str(CONFIGURE)
    ])


class SubTestResult(TextTestResult):
    def addSubTest(self, test, subtest, outcome):
        # handle failures calling base class
        super().addSubTest(test, subtest, outcome)
        # add to total number of tests run
        self.testsRun += 1


class TestConfigure(TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        build_configure()
        return super().setUpClass()

    def generate_tests(self, directory: Path) -> None:
        schema_path = ROOT_DIR / 'bin' / 'json_schemas' / 'complete_config_schema.json'

        if not directory.exists():
            return

        for entry in directory.iterdir():
            config_file = entry / 'config' / 'config.json'
            if not config_file.exists():
                continue
            with self.subTest(config=entry.name):
                with NamedTemporaryFile() as temp_file_1, NamedTemporaryFile() as temp_file_2:
                    subprocess.check_call(['cpp', str(config_file), temp_file_1.name])
                    subprocess.check_call(['python3', str(GRADING_DIR / 'json_syntax_checker.py'), temp_file_1.name])
                    subprocess.check_call(
                        [str(CONFIGURE), temp_file_1.name, temp_file_2.name, 'test_assignment'],
                        stdout=subprocess.PIPE,
                        stderr=subprocess.PIPE
                    )
                    try:
                        submitty_schema_validator.validate_complete_config_schema_using_filenames(temp_file_2.name, schema_path)
                    except submitty_schema_validator.SubmittySchemaException as s:
                        s.print_human_readable_error()
                        raise
                    subprocess.check_call(['cp', temp_file_2.name, str(THIS_DIR / (entry.name + '.json'))])

    def test_configure(self):
        print("Running test suite")

        self.generate_tests(AUTOGRADING_EXAMPLES)
        self.generate_tests(TUTORIAL_EXAMPLES)


if __name__ == "__main__":
    main(testRunner=TextTestRunner(resultclass=SubTestResult), verbosity=5)
