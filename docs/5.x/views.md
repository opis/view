---
layout: project
version: 5.x
title: Views
---
# Views

1. [Introduction](#introduction)
2. [View's name](#views-name)
3. [View's arguments](#views-arguments)

## Introduction

A *view* is nothing more but an instance of a class that implements the `Opis\View\IView` interface.
The interface has two methods: `viewName` and `viewAruments`. The first method must return the
name of the view, while the other one must return an array of values.

The default implementation of a *view* is provided by the library itself, through its `Opis\View\View` class. 
The constructor of this class takes as arguments the view name and, optionally, an array of values.

## View's name

## View's arguments