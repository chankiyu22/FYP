#include <iostream>
#include "./Vocab.h"
#include "./Document.h"
using namespace std;

int main(int argc, char* argv[]) {
  if (argc != 5 && argc != 7) {
    cout << "Usage: " << argv[0] << " -s [document source] [-n [number of documents]] -e [stop word file]" << endl;
    return -1;
  }
  string source_docs;
  string exclude_file;
  int num_docs = 0;
  for (int i = 1; i < argc; i++) {
    if (strcmp(argv[i], "-s") == 0) {
      source_docs = argv[i + 1];
      i = i + 1;
    } else if (strcmp(argv[i], "-n") == 0) {
      num_docs = atoi(argv[i + 1]);
      i = i + 1;
    } else if (strcmp(argv[i], "-e") == 0) {
      exclude_file = argv[i + 1];
      i = i + 1;
    }
  }
  char* docs_cstr = new char[source_docs.length() + 1];
  char* excl_cstr = new char[exclude_file.length() + 1];
  strcpy(docs_cstr, source_docs.c_str());
  strcpy(excl_cstr, exclude_file.c_str());

  Vocab vocab;

  vocab.setExclusion(excl_cstr);

  cout << "Extracting Vocaburary..." << endl;
  vocab.extractVocab(docs_cstr, num_docs);

  vocab.printToFile("./vocab.txt");

  Document docs(&vocab, docs_cstr);

  cout << "Counting Vocabs..." << endl;
  docs.count(num_docs);

  return 0;
}
