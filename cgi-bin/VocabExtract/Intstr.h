#ifndef intstr_h
#define intstr_h

#include "Str_cell.h"

/**
 * CLASS Intstr
 * It is a class managing relations between strings and integers.
 *
 * Basically, the integer is related to the sum of character in a string.
 *
 * Implementation:
 * Consider a bunch of strings, this class manage and sort strings using
 * the sum of character of that string. If more string have the same sum,
 * the strings will be managed by Str_cell class.
 * 
 * Intstr Cells will be connected by unidirection pointer. The order is 
 * increasing w.r.t. the character sum of string.
 */
class Intstr {
public:
  Intstr();
  Intstr(int sum, char* str);
  int strSum();

  Str_cell* strCell();
  Intstr* next();

  void setNext(Intstr* next_cell);
private:
  int sum;
  Str_cell* _str_cell;
  Intstr* _next;
};

/**
 * Function insert
 * Insert a new string into the original tree
 * according to the character sum.'
 */
void insert(Intstr*& start,int sum, char* str);
#endif
