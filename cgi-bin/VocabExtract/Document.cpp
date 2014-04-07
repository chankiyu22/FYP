#include "Document.h"

Document::Document(Vocab* vocab, char* filename) {
  this->vocab = vocab;
  ifs.open(filename);
  ofs.open("wordcount.txt");
}

void Document::count(int num_docs) {
  string word, nword;
  int x = 0;
  while (!ifs.eof()) {
    ifs >> word;
    if (strcmp(word.c_str(), "\"text\":") == 0) {
      if (x == num_docs && num_docs != 0)
        break;
      x++;
      if (x % 1000 == 0)
        cout << "  Document " << x << endl;
      vocab->initCount();
      while (true) {
        ifs >> word;
        if (strcmp(word.c_str(), "\"type\":") == 0) {
          vocab->printCountToFile(ofs);
          break;
        }

        nword = normalize(word);
        if (!isalpha(nword[0]))
          continue;
        nword[0] = toupper(nword[0]);
        
        vocab->addCount(nword);
      }
    }
  }
}
