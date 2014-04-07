#include "Vocab.h"

Vocab::Vocab() {
  vocab_list = NULL;
  counter = NULL;
}

void Vocab::setExclusion(char* filename) {
  ifstream ifs(filename);
  string word;
  while (!ifs.eof()) {
    ifs >> word;
    word[0] = toupper(word[0]);
    char* s = new char[word.length() + 1];
    strcpy(s, word.c_str());
    s[stem(s, 0, word.length() - 1) + 1] = 0;
    exclusion_list.insert(exclusion_list.end(), s);
    delete s;
  }

  ifs.close();
}

string normalize(string word) {
  for (int i = 0; i < word.length(); i++) {
    if (strncmp(word.c_str() + i, "\\n", 2) == 0 || 
        strncmp(word.c_str() + i, "\\\"", 2) == 0)
      word.replace(word.begin() + i, word.begin() + i + 2, " ");
    if (word[i] == '\"' || word[i] == ',' || word[i] == '.' || word[i] == '!' ||
        word[i] == '(' || word[i] == ')' || word[i] == ';' || word[i] == ':' ||
        word[i] == '?' || word[i] == '-' || word[i] == '+' || word[i] == '/') {
      word.erase(word.begin() + i);
      i--;
    }
  }
  return word;
}

bool Vocab::exclusionExists(char* s) {
  for (int i = 0; i < exclusion_list.size(); i++) {
    if (strcmp(s, exclusion_list[i].c_str()) == 0) {
      return true;
    }
  }
  return false;
}  

void Vocab::extractVocab(char* filename, int num_docs) {
  ifstream ifs(filename);
  string word;
  string nword;
  int docno = 0;
  while (!ifs.eof()) {
    ifs >> word;
    if (strcmp(word.c_str(), "\"text\":") == 0) {
      if (docno == num_docs && num_docs != 0)
        break;
      docno++;
      if (docno % 1000 == 0)
        cout << "  Document " << docno << endl;
      while (true) {
        ifs >> word;
        if (strcmp(word.c_str(), "\"type\":") == 0)
          break;
        nword = normalize(word);
        if (!isalpha(nword[0]))
          continue;
        nword[0] = toupper(nword[0]);

        char* s = new char[nword.length() + 1];
        strcpy(s, nword.c_str());
        s[stem(s, 0, nword.length() - 1) + 1] = 0;
        if (exclusionExists(s)) {
          /** DO NOTHING **/
        } else {
          // TODO: add to vocab_list
          int sum = 0;
          for (int i = 0; i < nword.length(); i++)
            sum += (int)nword[i];

          char* nword_cstr = new char[nword.length() + 1];
          strcpy(nword_cstr, nword.c_str());

          if (vocab_list == NULL)
            vocab_list = new Intstr(sum, nword_cstr);
          else
            insert(vocab_list, sum, nword_cstr);
          delete nword_cstr;
        }
        delete s;
      }
    }
  }
  
  ifs.close();
}

void Vocab::printToFile(char* filename) {
  ofstream ofs(filename);
  Intstr* it = vocab_list;
  Str_cell* sit;
  while(it != NULL) {
    sit = it->strCell();
    while (sit != NULL) {
      ofs << sit->str() << endl;
      sit = sit->next();
    }
    it = it->next();
  }
}

void Vocab::initCount() {
  if (counter == NULL) {
    numvocab = getNumVocab();
    counter = new int[numvocab];
    for (int i = 0; i < numvocab; i++) {
      counter[i] = 0;
    }

    int i = 0;
    Intstr* it = vocab_list;
    while (it != NULL) {
      Str_cell* sit = it->strCell();
      while (sit != NULL) {
        sit->linkToCount(counter + i);
        i++;
        sit = sit->next();
      }
      it = it->next();
    }
  }
  else {
    for (int i = 0; i < numvocab; i++) {
      counter[i] = 0;
    }
  }
}

void Vocab::printCountToFile(ofstream& ofs) {
  ofs << getExistVocab() << ' ';
  for (int i = 0; i < numvocab; i++) {
    if (counter[i] != 0) {
      ofs << i << ':' << counter[i] << ' ';
    }
  }
  ofs << endl;
}

void Vocab::addCount(string s) {
  int sum = 0;
  for (int i = 0; i < s.size(); i++) {
    sum += (int)s[i];
  }

  for (Intstr* it = vocab_list; it != NULL; it = it->next()) {
    if (sum < it->strSum())
      return;
    if (sum == it->strSum()) {
      for (Str_cell* sit = it->strCell(); sit != NULL; sit = sit->next()) {
        if (strcmp(s.c_str(), sit->str()) == 0)
          sit->addCount();
      }
    }
  }
}

int Vocab::getNumVocab() {
  int num = 0;
  for (Intstr* it = vocab_list; it != NULL; it = it->next()) 
    for (Str_cell* sit = it->strCell(); sit != NULL; sit = sit->next())
      num++;
  return num;
}

int Vocab::getExistVocab() {
  int num = 0;
  for (int i = 0; i < numvocab; i++)
    if (counter[i] != 0)
      num++;
  return num;
}
